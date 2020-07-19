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

import amd from 'tui/amd';
import pending from 'tui/pending';

jest.mock('tui/pending');

global.requirejs = jest.fn((mods, cb) => cb());

describe('amd', () => {
  it('calls requirejs global', async () => {
    await amd('foo');
    expect(global.requirejs).toHaveBeenCalledWith(
      ['foo'],
      expect.any(Function),
      expect.any(Function)
    );
  });

  it('calls pending()', async () => {
    expect(pending.__started('amd:gnidnep')).toBe(0);
    const result = amd('gnidnep');
    expect(pending.__started('amd:gnidnep')).toBe(1);
    await result;
    expect(pending.__completed('amd:gnidnep')).toBe(1);
  });

  it('returns amd module', async () => {
    const mod = {};
    global.requirejs = jest.fn((mods, cb) => {
      cb(mod);
    });
    const result = await amd('foo');
    expect(result).toBe(mod);
  });

  it('caches result', async () => {
    const mod = {};
    global.requirejs = jest.fn((mods, cb) => {
      cb(mod);
    });
    expect(await amd('bar')).toBe(mod);
    expect(await amd('bar')).toBe(mod);
    expect(global.requirejs).toHaveBeenCalledTimes(1);
  });
});
