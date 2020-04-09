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
  return memoizeWithMap(fn, keyFn, new Map());
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
  return memoizeWithMap(fn, keyFn, new WeakMap());
}

/**
 * Create a memoized version of the provided function.
 *
 * Like `memoize()` but using a the map you pass for key storage.
 *
 * @private
 * @param {function} fn
 * @param {function=} keyFn
 * @param {object} map Object implementing Map API (has/get/set)
 * @returns {function}
 */
function memoizeWithMap(fn, keyFn, map) {
  return function(arg) {
    const key = keyFn ? keyFn.apply(this, arguments) : arg;
    if (map.has(key)) {
      return map.get(key);
    }
    const result = fn.apply(this, arguments);
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
