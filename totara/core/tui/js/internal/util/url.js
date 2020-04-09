/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

import { globalConfig } from '../../config';

/**
 * Format a URL parameter
 *
 * @private
 * @param {string} key Parameter name
 * @param {*} value Parameter value
 */
function formatParam(key, value) {
  if (Array.isArray(value)) {
    return value
      .map((nestedVal, nestedKey) =>
        formatParam(key + '[' + encodeURIComponent(nestedKey) + ']', nestedVal)
      )
      .join('&');
  } else if (typeof value == 'object') {
    return Object.keys(value)
      .map(nestedKey =>
        formatParam(
          key + '[' + encodeURIComponent(nestedKey) + ']',
          value[nestedKey]
        )
      )
      .join('&');
  } else {
    return key + '=' + encodeURIComponent(value);
  }
}

/**
 * Format the provided parameters into a string separated by &
 *
 * @param {object=} params URL parameters.
 *   Map of keys to values.
 *   Objects and arrays are acceped as values and encoded using [].
 */
export function formatParams(params) {
  return Object.entries(params)
    .map(([key, value]) => formatParam(key, value))
    .join('&');
}

/**
 * Generate URL
 *
 * @param {string} url Absolute url or path beginning with /
 *   e.g. '/foo/bar.php', 'https://www.google.com/'
 * @param {object=} params URL parameters.
 *   Map of keys to values.
 *   Objects and arrays are acceped as values and encoded using [].
 */
export function url(url, params) {
  // prepend with wwwroot if not absolute
  if (!/^(?:[a-z]+:)?\/\//.test(url)) {
    if (url[0] != '/') {
      throw new Error('`url` must be an absolute URL or begin with a /');
    }
    url = globalConfig.wwwroot + url;
    // if URL constructor is supported, pass it through to test that the url is valid
    if (typeof URL == 'function') {
      new URL(url);
    }
  }

  const formattedParams = params && formatParams(params);
  if (formattedParams) {
    if (!url.includes('?')) {
      url += '?';
    }
    if (!url.endsWith('?') && !url.endsWith('&')) {
      url += '&';
    }
    url += formattedParams;
  }

  return url;
}
