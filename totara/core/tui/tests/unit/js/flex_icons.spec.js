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

import amd from 'totara_core/amd';

jest.mock('totara_core/amd');
jest.mock('totara_core/pending');

const amdFlexIcon = {
  load: jest.fn().mockResolvedValue(),
  getFlexTemplateDataSync: jest.fn(x => ({ id: x })),
};

amd.__setMock('core/flex_icon', amdFlexIcon);

describe('load', () => {
  it('should be memoized', async () => {
    let flexIcons;
    jest.isolateModules(() => {
      flexIcons = require('totara_core/flex_icons');
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
      flexIcons = require('totara_core/flex_icons');
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
      flexIcons = require('totara_core/flex_icons');
    });
    await flexIcons.load();
    expect(flexIcons.getFlexData('foo')).toEqual({ id: 'foo' });
    expect(amdFlexIcon.getFlexTemplateDataSync).toHaveBeenCalledTimes(1);
  });

  it('throws an exception when data not loaded', () => {
    let flexIcons;
    jest.isolateModules(() => {
      flexIcons = require('totara_core/flex_icons');
    });
    expect(() => flexIcons.getFlexData('foo')).toThrow('not loaded');
  });
});
