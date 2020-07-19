/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * Object.prototype.hasOwnProperty wrapper.
 *
 * @param {object} obj
 * @param {string} key
 * @returns {boolean}
 */
export function hasOwnProperty(obj, key) {
  return Object.prototype.hasOwnProperty.call(obj, key);
}

const objectConstructorString = Function.prototype.toString.call(Object);

/**
 * Check if an object is a plain object (i.e. {}), not a class instance etc.
 *
 * @param {*} value
 * @returns {boolean}
 */
export function isPlainObject(value) {
  if (!value || typeof value != 'object') {
    return false;
  }
  const proto = Object.getPrototypeOf(value);
  if (proto == null) {
    return true;
  }
  const ctor = hasOwnProperty(proto, 'constructor') && proto.constructor;
  return (
    typeof ctor == 'function' &&
    ctor instanceof ctor &&
    Function.prototype.toString.call(ctor) === objectConstructorString
  );
}

/**
 * Structural deep clone.
 *
 * Only clones plain objects and arrays, other values are left alone.
 *
 * @param {(object|array)} obj
 * @return {(object|array)}
 */
export function structuralDeepClone(obj) {
  return structuralCloneImpl(obj, true, []);
}

/**
 * Structural shallow clone.
 *
 * Only clones plain objects and arrays, other values are left alone.
 *
 * @param {(object|array)} obj
 * @return {(object|array)}
 */
export function structuralShallowClone(obj) {
  return structuralCloneImpl(obj, false);
}

function structuralCloneImpl(obj, deep, stack) {
  if (stack && stack.includes(obj)) {
    throw new TypeError('Cannot clone circular structure.');
  }

  let result = obj;
  const isArr = Array.isArray(obj),
    isObj = isPlainObject(obj);

  if (isArr || isObj) {
    if (isArr) {
      result = [];
    } else {
      result = {};
    }

    for (let key in obj) {
      if (hasOwnProperty(obj, key)) {
        let substack;
        if (deep) {
          substack = stack.slice();
          substack.push(result);
        }
        result[key] = deep
          ? structuralCloneImpl(obj[key], deep, substack)
          : obj[key];
      }
    }
  }

  return result;
}

/**
 * Get the value of path at object.
 *
 * @param {object} object
 * @param {array} path Path of property to get, e.g. ['a', 2, 'q']
 * @returns {*} Value at path.
 */
export function get(object, path) {
  if (typeof path === 'string') {
    return object[path];
  }

  let i = 0;
  while (object != null && i < path.length) {
    object = object[path[i++]];
  }
  return object;
}

export const isIndex = val =>
  typeof val == 'number' || /^(?:0|[1-9]\d*)$/.test(val);

const setKey = (target, key, value) => {
  target[key] = value;
};

/**
 * Set the value at path of object.
 *
 * Arrays are created for missing index properties and objects are created for other missing properties.
 *
 * @param {object} object Object to modify.
 * @param {array} path Path of property to set, e.g. ['a', 2, 'q']
 * @param {*} value
 * @returns {object}
 */
export function set(object, path, value) {
  return baseSet(object, path, value, setKey);
}

/**
 * Base implementation for set and vue_util.set.
 *
 * @param {object} object Object to modify.
 * @param {array} path Path of property to set, e.g. ['a', 2, 'q']
 * @param {*} value
 * @param {*} setKey
 * @returns {object}
 */
export function baseSet(object, path, value, setKey) {
  if (typeof path === 'string') {
    setKey(object, path, value);
    return;
  }

  const lastIndex = path.length - 1;
  let index = 0;

  while (object != null && index <= lastIndex) {
    const key = path[index];
    if (index == lastIndex) {
      setKey(object, key, value);
    } else {
      if (object[key] == null) {
        // no object at key, create one
        setKey(object, key, isIndex(path[index + 1]) ? [] : {});
      }
    }
    object = object[key];
    index++;
  }
  return object;
}
