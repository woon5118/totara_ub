/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @module tui
 */

import { v, fieldValidator } from 'tui/validation';

jest.mock('tui/i18n', () => {
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
