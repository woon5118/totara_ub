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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @module totara_engage
 */

const slice = Array.prototype.slice;

/**
 * Returning an array of array.
 *
 * @param {Object[]} arrayData
 * @param {Number} perRow
 *
 * @return {Array}
 */
export function calculateRow(arrayData, perRow) {
  if ('undefined' === typeof arrayData || 0 === arrayData.length) {
    return [];
  }

  arrayData = slice.call(arrayData);
  let rows = [];

  for (let index = 0; index < arrayData.length; index += perRow) {
    let row = slice.call(arrayData, index, index + perRow);
    rows.push({ index: rows.length, items: row });
  }

  return rows;
}

export const engageGrid = {
  xsmall: {
    name: 'xsmall',
    boundaries: [0, 480],
    direction: 'horizontal',
    maxItemsPerRow: 1,
    cardUnits: 3,
  },

  small: {
    name: 'small',
    boundaries: [481, 794],
    direction: 'horizontal',
    maxItemsPerRow: 2,
    cardUnits: 3,
  },

  medium: {
    name: 'medium',
    boundaries: [795, 1089],
    direction: 'horizontal',
    maxItemsPerRow: 3,
    cardUnits: 3,
  },

  large: {
    name: 'large',
    boundaries: [1090, 1396],
    direction: 'horizontal',
    maxItemsPerRow: 4,
    cardUnits: 2,
  },

  xlarge: {
    name: 'xlarge',
    boundaries: [1397, 1672],
    direction: 'horizontal',
    maxItemsPerRow: 4,
    cardUnits: 2,
  },
};
