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

import { v, fieldValidator } from 'totara_core/validation';

jest.mock('totara_core/i18n', () => {
  return {
    langString(str) {
      return str;
    },
  };
});

describe('built-in validators', () => {
  test('required', () => {
    const i = v.required();
    expect(i.validate()).toBe(false);
    expect(i.validate('')).toBe(false);
    expect(i.validate('    ')).toBe(false);
    expect(i.validate('hi')).toBe(true);
    expect(i.validate(0)).toBe(false);
    expect(i.validate([])).toBe(false);
    expect(i.validate({})).toBe(true);
    expect(i.validate('0')).toBe(true);
  });

  test('email', () => {
    const i = v.email();
    expect(i.validate('foo')).toBe(false);
    expect(i.validate('a@b.com')).toBe(true);
    expect(i.validate('@b')).toBe(false);
  });

  test('number', () => {
    const i = v.number();
    expect(i.validate(0)).toBe(true);
    expect(i.validate(1)).toBe(true);
    expect(i.validate(1.1)).toBe(true);
    expect(i.validate('0')).toBe(true);
    expect(i.validate('1')).toBe(true);
    expect(i.validate('1,000')).toBe(false);
    expect(i.validate('1.1')).toBe(true);
    expect(i.validate('$1')).toBe(false);
    expect(i.validate('.')).toBe(false);
    expect(i.validate('hello')).toBe(false);
  });

  test('integer', () => {
    const i = v.integer();
    expect(i.validate(0)).toBe(true);
    expect(i.validate(1)).toBe(true);
    expect(i.validate(1.1)).toBe(false);
    expect(i.validate('0')).toBe(true);
    expect(i.validate('1')).toBe(true);
    expect(i.validate('1,000')).toBe(false);
    expect(i.validate('1.1')).toBe(false);
    expect(i.validate('$1')).toBe(false);
    expect(i.validate('.')).toBe(false);
    expect(i.validate('hello')).toBe(false);
  });

  test('minLength', () => {
    const i = v.minLength(3);
    expect(i.validate('')).toBe(false);
    expect(i.validate('aa')).toBe(false);
    expect(i.validate('aaa')).toBe(true);
    expect(i.validate('aaaa')).toBe(true);
  });

  test('maxLength', () => {
    const i = v.maxLength(3);
    expect(i.validate('')).toBe(true);
    expect(i.validate('aa')).toBe(true);
    expect(i.validate('aaa')).toBe(true);
    expect(i.validate('aaaa')).toBe(false);
  });

  test('min', () => {
    const i = v.min(3);
    expect(i.validate(2)).toBe(false);
    expect(i.validate(3)).toBe(true);
    expect(i.validate(4)).toBe(true);
    expect(i.validate('2')).toBe(false);
    expect(i.validate('3')).toBe(true);
    expect(i.validate('4')).toBe(true);
  });

  test('max', () => {
    const i = v.max(3);
    expect(i.validate(2)).toBe(true);
    expect(i.validate(3)).toBe(true);
    expect(i.validate(4)).toBe(false);
    expect(i.validate('2')).toBe(true);
    expect(i.validate('3')).toBe(true);
    expect(i.validate('4')).toBe(false);
  });
});

describe('fieldValidator', () => {
  it('creates a validator function for a field', () => {
    const i = fieldValidator(v => [v.required(), v.min(3)]);
    expect(i('7')).toBe(undefined);
    expect(i('')).toBe('required');
    expect(i('2')).toBe('Number must be 3 or more');
  });
});
