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

import {
  getString,
  hasString,
  unloadedStrings,
  loadStrings,
} from 'tui/i18n';
import {
  loadStrings as rawLoadStrings,
  getString as rawGetString,
  hasString as rawHasString,
} from 'tui/internal/lang_string_store';

jest.unmock('tui/i18n');
jest.mock('tui/internal/lang_string_store');

beforeEach(() => {
  rawLoadStrings.mockClear();
  rawGetString.mockClear();
  rawHasString.mockClear();
});

describe('getString', () => {
  it('wraps raw', () => {
    expect(getString('bar', 'foo', 'c')).toBe('baz');
    expect(rawGetString).toHaveBeenCalledWith('bar', 'foo');
    expect(getString('baz', 'foo')).toBe('qux');
    expect(rawGetString).toHaveBeenCalledWith('baz', 'foo');
  });

  it('replaces placeholders', () => {
    expect(getString('replace', 'foo', 'bob')).toBe('hello bob');
    expect(rawGetString).toHaveBeenCalledWith('replace', 'foo');
    expect(
      getString('replace_complex', 'foo', { name: 'bob', weather: 'sunny' })
    ).toBe('hello bob, today is sunny');
    expect(rawGetString).toHaveBeenCalledWith('replace_complex', 'foo');
    expect(getString('replace_complex', 'foo', { name: 'bob' })).toBe(
      'hello bob, today is {$a->weather}'
    );
  });

  it('normalizes component', () => {
    expect(getString('save', 'core')).toBe('Save');
    expect(rawGetString).toHaveBeenCalledWith('save', 'moodle');
    expect(getString('save')).toBe('Save');
    expect(rawGetString).toHaveBeenCalledWith('save', 'moodle');
  });
});

describe('hasString', () => {
  it('wraps raw', () => {
    expect(hasString('bar', 'foo')).toBe(true);
    expect(rawHasString).toHaveBeenCalledWith('bar', 'foo');
    expect(hasString('bar', 'a')).toBe(false);
    expect(rawHasString).toHaveBeenCalledWith('bar', 'a');
    expect(hasString('b', 'foo')).toBe(false);
    expect(rawHasString).toHaveBeenCalledWith('b', 'foo');
  });

  it('normalizes component', () => {
    expect(hasString('f', 'core')).toBe(false);
    expect(rawHasString).toHaveBeenCalledWith('f', 'moodle');
    expect(hasString('save', 'core')).toBe(true);
    expect(rawHasString).toHaveBeenCalledWith('save', 'moodle');
    expect(hasString('f')).toBe(false);
    expect(rawHasString).toHaveBeenCalledWith('f', 'moodle');
  });
});

describe('unloadedStrings', () => {
  it('filters out strings which are already loaded', () => {
    expect(
      unloadedStrings([
        {
          component: 'foo',
          key: 'bar',
        },
        {
          component: 'a',
          key: 'b',
        },
      ])
    ).toEqual([
      {
        component: 'a',
        key: 'b',
      },
    ]);
  });

  it('normalizes component', () => {
    expect(
      unloadedStrings([
        {
          component: 'core',
          key: 'save',
        },
      ])
    ).toEqual([]);
  });
});

describe('loadStrings', () => {
  it('calls raw loadStrings with the provided strings', async () => {
    const requests = [{ component: 'a', key: 'b' }];
    expect(await loadStrings(requests)).toBe(undefined);
    expect(rawLoadStrings).toHaveBeenCalledWith(requests);
  });

  it('normalizes', async () => {
    const requests = [{ component: 'core', key: 'save' }];
    expect(await loadStrings(requests)).toBe(undefined);
    expect(rawLoadStrings).toHaveBeenCalledWith([
      { component: 'moodle', key: 'save' },
    ]);
  });
});
