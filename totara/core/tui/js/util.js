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

import { globalConfig } from './config';

let counter = 1;

/**
 * Generates an incrementing unique ID.
 *
 * This number is only unique within a page, and is not unique across page
 * loads.
 *
 * @return {number}
 */
export function uniqueId() {
  return counter++;
}

/**
 * Remove the first instance of the specified item from the array.
 *
 * @param {array} array
 * @param {*} item
 */
export function pull(array, item) {
  const index = array.indexOf(item);
  if (index != -1) {
    array.splice(index, 1);
  }
}

/**
 * Return a copy of array filtered to only contain unique values.
 *
 * @param {array} arr
 * @returns {array}
 */
export function unique(arr) {
  return arr.filter((item, pos) => arr.indexOf(item) === pos);
}

/**
 * Create a new object composed of the selected keys of the provided object.
 *
 * @param {object} object
 * @param {array} keys
 * @return {object}
 */
export function pick(object, keys) {
  if (!Array.isArray(keys)) {
    throw new Error('keys must be an array');
  }
  const newObj = {};
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    if (key in object) {
      newObj[key] = object[key];
    }
  }
  return newObj;
}

/**
 * Splits a collection into sets, group by the result of running each value
 * through `fn`.
 *
 * @param {*} array
 * @param {*} fn
 */
export function groupBy(array, fn) {
  const result = {};
  array.forEach(x => {
    const key = fn(x);
    if (!result[key]) {
      result[key] = [];
    }
    result[key].push(x);
  });
  return result;
}

/**
 * Get a result from a value.
 *
 * If value is a function it will be called to obtain the result, otherwise
 * value will be used as-is.
 *
 * @param {*} value
 * @return {*}
 */
export function result(value) {
  if (value instanceof Function) {
    return value();
  }
  return value;
}

/**
 * Create a memoized version of the provided function.
 *
 * By default, the first argument to the function is used as the cache key.
 * If you pass a function as `keyFn`, it will be called with the invocation's
 * arguments to generate a cache key.
 *
 * The cache key can be anything, it is not restricted to being a string.
 *
 * @param {function} fn
 * @param {function=} keyFn
 * @returns {function}
 */
export function memoize(fn, keyFn) {
  return memoizeInternal(fn, keyFn, new Map());
}

/**
 * Create a memoized version of the provided function.
 *
 * Like `memoize()` but using a WeakMap.
 *
 * @param {function} fn
 * @param {function=} keyFn
 * @returns {function}
 */
export function memoizeWeak(fn, keyFn) {
  return memoizeInternal(fn, keyFn, new WeakMap());
}

/**
 * Common internal memoize implementation.
 *
 * @private
 * @param {function} fn
 * @param {function=} keyFn
 * @param {object} map Object implementing Map API (has/get/set)
 * @returns {function}
 */
function memoizeInternal(fn, keyFn, map) {
  return function(arg) {
    const key = keyFn ? keyFn.apply(null, arguments) : arg;
    if (map.has(key)) {
      return map.get(key);
    }
    const result = fn.apply(null, arguments);
    map.set(key, result);
    return result;
  };
}

/**
 * Wrap the provided async or promise-returning function so that subsequent
 * calls get the same result, and calls while loading get the same promise
 * to avoid making multiple requests.
 *
 * @param {function} fn
 * @return {Promise}
 */
export function memoizeLoad(fn) {
  let promise;
  const memoized = () => {
    if (promise) {
      return promise;
    }

    promise = fn();
    promise.catch(() => (promise = null));
    return promise;
  };

  /* istanbul ignore next */
  if (process.env.NODE_ENV == 'test') {
    memoized._fn = fn;
    memoized._change = newFn => {
      memoize._fn = fn = newFn;
      promise = null;
    };
  }
  return memoized;
}

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
