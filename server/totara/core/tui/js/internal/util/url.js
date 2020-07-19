/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD’s customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module totara_core
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
