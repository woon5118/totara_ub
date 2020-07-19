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
} from 'totara_core/i18n';
import amd from 'totara_core/amd';

jest.mock('totara_core/amd');
jest.mock('totara_core/pending');

const M = (global.M = {
  util: {
    get_string: jest.fn((...args) => args.join(',')),
  },
  str: {
    foo: {
      bar: 'baz',
    },
  },
});

const coreStr = {
  get_strings: jest.fn(x => x),
};

amd.__setMock('core/str', coreStr);

describe('getString', () => {
  it('wraps M.util.get_string', () => {
    expect(getString('a', 'b', 'c')).toBe('a,b,c');
    expect(M.util.get_string).toHaveBeenCalledWith('a', 'b', 'c');
    expect(getString('d', 'e')).toBe('d,e,');
    expect(M.util.get_string).toHaveBeenCalledWith('d', 'e', undefined);
    expect(getString('f')).toBe('f,,');
    expect(M.util.get_string).toHaveBeenCalledWith('f', undefined, undefined);
  });
});

describe('hasString', () => {
  it('wraps checks for presence in M.str', () => {
    expect(hasString('bar', 'foo')).toBe(true);
    expect(hasString('bar', 'a')).toBe(false);
    expect(hasString('b', 'foo')).toBe(false);
  });
});

describe('unloadedStrings', () => {
  it('filters out strings which are present in M.str', () => {
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
});

describe('loadStrings', () => {
  it("calls amd('core/str').get_strings with the provided strings", async () => {
    const requests = [{ component: 'a', key: 'b' }];
    expect(await loadStrings(requests)).toBe(undefined);
    expect(coreStr.get_strings).toHaveBeenCalledWith(requests);
  });
});
