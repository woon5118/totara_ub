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

import { langString } from './i18n';
import { isExists } from 'date-fns';
import { isIsoAfter, isIsoBefore, getValuesFromIso } from 'totara_core/date';

// match browser email validation regex.
const EMAIL_REGEX = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

// built-in validations
export const v = {
  required: () => ({
    validate: val => {
      const isEmpty =
        !val ||
        (typeof val === 'string' && val.trim().length === 0) ||
        (Array.isArray(val) && val.length === 0);
      return !isEmpty;
    },
    message: () => langString('required', 'moodle'),
  }),

  date: () => ({
    validate: val => {
      const date = getValuesFromIso(val.iso);
      return isExists(date.year, date.month, date.day);
    },
    message: () => langString('not_a_valid_date', 'totara_form'),
  }),

  /**
   * Check date is not greater than max limit
   *
   * @param {isoDate} limit
   * @param {isoDate} customErrorString
   */
  dateMaxLimit: (limit, customErrorString) => ({
    validate: val => {
      return isIsoBefore(val.iso, limit);
    },
    message: () => {
      if (customErrorString) {
        return customErrorString;
      }
      return langString('date_after_limit', 'totara_form', limit);
    },
  }),

  /**
   * Check date is greater than min limit
   *
   * @param {isoDate} limit
   * @param {isoDate} customErrorString
   */
  dateMinLimit: (limit, customErrorString) => ({
    validate: val => {
      return isIsoAfter(val.iso, limit);
    },
    message: () => {
      if (customErrorString) {
        return customErrorString;
      }
      return langString('date_before_limit', 'totara_form', limit);
    },
  }),

  email: () => ({
    validate: val => EMAIL_REGEX.test(val),
    message: () => langString('emailvalidationerror', 'totara_form'),
  }),

  number: () => ({
    validate: val => !isNaN(Number(val)),
    // TODO: language strings
    message: () => `Please enter a valid number`,
  }),

  integer: () => ({
    validate: val => {
      const num = Number(val);
      // not NaN and is an integer
      return !isNaN(num) && (num | 0) === num;
    },
    message: () => `Please enter a valid whole number`,
  }),

  minLength: len => ({
    validate: val => val.length >= len,
    message: () => `Please enter at least ${len} characters`,
  }),

  maxLength: len => ({
    validate: val => val.length <= len,
    message: () => `Please enter at no more than ${len} characters`,
  }),

  min: min => ({
    validate: val => Number(val) >= min,
    message: () => `Number must be ${min} or more`,
  }),

  max: max => ({
    validate: val => Number(val) <= max,
    message: () => `Number must be ${max} or less`,
  }),

  // for testing async validation
  delay: (t = 1000) => ({
    validate: () => new Promise(r => setTimeout(() => r(true), t)),
    message: () => {},
  }),
};

/**
 * Make validator function from a validator spec.
 *
 * @param {*} validator
 */
function makeValidator(validator) {
  if (typeof validator == 'function') {
    return validator;
  }
  return value => {
    const result = validator.validate(value);
    if (result && result.then) {
      return result.then(() => {
        if (!result) {
          return validator.message();
        }
      });
    }
    if (!result) {
      return validator.message();
    }
  };
}

/**
 * Make validator function for a single field.
 *
 * @param {(function|array)} validators
 * @returns {function}
 */
export function fieldValidator(validators) {
  // ensure validators are in the correct format
  if (typeof validators == 'function') {
    validators = validators(v);
  }
  if (!Array.isArray(validators)) {
    validators = [validators];
  }

  validators = validators.map(makeValidator);

  return value => {
    let error = null;
    for (var i = 0; i < validators.length; i++) {
      error = validators[i](value);
      if (error) {
        break;
      }
    }
    return error;
  };
}
