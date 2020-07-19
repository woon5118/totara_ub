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

jest.mock('tui/amd');
jest.mock('tui/pending');

const amdFlexIcon = {
  load: jest.fn().mockResolvedValue(),
  getFlexTemplateDataSync: jest.fn(x => ({ id: x })),
};

amd.__setMock('core/flex_icon', amdFlexIcon);

describe('load', () => {
  it('should be memoized', async () => {
    let flexIcons;
    jest.isolateModules(() => {
      flexIcons = require('tui/flex_icons');
    });
    var originalLoad = flexIcons.load._fn;
    var fn = jest.fn(async () => {});
    flexIcons.load._change(fn);
    expect(fn).toHaveBeenCalledTimes(0);
    await flexIcons.load();
    expect(fn).toHaveBeenCalledTimes(1);
    flexIcons.load._change(originalLoad);
  });

  it("should call amd('core/flex_icon').load() and wait", async () => {
    let flexIcons;
    jest.isolateModules(() => {
      flexIcons = require('tui/flex_icons');
    });
    expect(flexIcons.loaded()).toBe(false);
    await flexIcons.load();
    expect(amdFlexIcon.load).toHaveBeenCalledTimes(1);
    expect(flexIcons.loaded()).toBe(true);
  });
});

describe('getFlexData', () => {
  it('calls getFlexTemplateDataSync() to get icon data', async () => {
    let flexIcons;
    jest.isolateModules(() => {
      flexIcons = require('tui/flex_icons');
    });
    await flexIcons.load();
    expect(flexIcons.getFlexData('foo')).toEqual({ id: 'foo' });
    expect(amdFlexIcon.getFlexTemplateDataSync).toHaveBeenCalledTimes(1);
  });

  it('throws an exception when data not loaded', () => {
    let flexIcons;
    jest.isolateModules(() => {
      flexIcons = require('tui/flex_icons');
    });
    expect(() => flexIcons.getFlexData('foo')).toThrow('not loaded');
  });
});
