/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @module tui
 */

import { getDateOrderFromStrftime } from 'tui/date';

describe('getDateOrderFromStrftime', () => {
  it('Handles english', () => {
    const order = getDateOrderFromStrftime('%d/%m/%Y');
    expect(order).toEqual(['d', 'm', 'y']);
  });

  it('Handles obscure formats', () => {
    const order = getDateOrderFromStrftime('%A  -  %B   -  %Y');
    expect(order).toEqual(['d', 'm', 'y']);
  });

  it('Handles the default output as the input format', () => {
    const order = getDateOrderFromStrftime('%y%m%d');
    expect(order).toEqual(['y', 'm', 'd']);
  });

  it('Handles multiple entries for the same date part', () => {
    let order = getDateOrderFromStrftime('%d%d/%m%m%m%m/%y%y%y');
    expect(order).toEqual(['d', 'm', 'y']);

    order = getDateOrderFromStrftime('%d/%m/%y %y/%d/');
    expect(order).toEqual(['d', 'm', 'y']);
  });

  it('Uses the default for spanish (missing year)', () => {
    const order = getDateOrderFromStrftime('%d/%m/%A');
    expect(order).toEqual(['y', 'm', 'd']); // Default value.
  });

  it('Uses the default for complete rubbish (missing all parts)', () => {
    const order = getDateOrderFromStrftime('rubbish');
    expect(order).toEqual(['y', 'm', 'd']); // Default value.
  });
});
