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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @module totara_core
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
