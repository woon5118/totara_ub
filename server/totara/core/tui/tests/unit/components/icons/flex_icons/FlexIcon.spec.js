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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_core
 */

import FlexIcon from 'totara_core/components/icons/flex_icons/FlexIcon';

describe('FlexIcon.vue', () => {
  it('Checks icon size validator', () => {
    expect(FlexIcon.props.size.validator('x')).toBe(false);
    expect(FlexIcon.props.size.validator('100')).toBe(true);
    expect(FlexIcon.props.size.validator(101)).toBe(false);
    expect(FlexIcon.props.size.validator(200)).toBe(true);
    expect(FlexIcon.props.size.validator('300')).toBe(true);
    expect(FlexIcon.props.size.validator(false)).toBe(false);
    expect(FlexIcon.props.size.validator(null)).toBe(false);
    expect(FlexIcon.props.size.validator(undefined)).toBe(false);
    expect(FlexIcon.props.size.validator(1.1)).toBe(false);
    expect(FlexIcon.props.size.validator('400')).toBe(true);
    expect(FlexIcon.props.size.validator(500)).toBe(true);
    expect(FlexIcon.props.size.validator('600')).toBe(true);
    expect(FlexIcon.props.size.validator('700')).toBe(true);
  });
});
