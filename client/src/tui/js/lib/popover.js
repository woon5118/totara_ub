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
 * @module totara_core
 */

// eslint-disable-next-line no-unused-vars
import { Rect, Point, Size } from 'tui/geometry';
import { langSide } from 'tui/i18n';

/**
 * @typedef {Object} PositionResult
 * @property {string} side Side of the reference to display the popover on.
 * @property {Point} location Location to render the popover.
 * @property {Number} arrowDistance Distance along the side to render the arrow at.
 */

/**
 * Calculate the position to place the popover.
 *
 * @param {string[]} position
 *   Requested position for the popover, e.g. ['top', 'left'], which would be
 *   at the 'left' of the 'top' side.
 *   This may not match the final position as the popopver may need to be moved
 *   in order to fit the available space.
 * @param {Rect} ref Rect describing the element to position the popover relative to.
 * @param {Rect} viewport Rect describing the visible part of the document.
 * @param {Size} size Size of the popover.
 * @param {Number} padding Padding around the popover (subtracted from size for some calculations).
 * @returns {PositionResult}
 */
export function position({ position, ref, viewport, size, padding }) {
  let [direction, subDirection] = position;

  direction = langSide(direction);
  subDirection = langSide(subDirection);

  /* istanbul ignore next */
  const tries = fallbackOrders[direction] || [direction];

  // try each side until we find one that fits
  for (var i = 0; i < tries.length; i++) {
    const offset = calculateOffset(
      tries[i],
      subDirection,
      ref,
      viewport,
      size,
      padding
    );
    if (offset) {
      return offset;
    }
  }

  // fallback: no room for popover in any direction :(
  return calculateOffset(
    'bottom',
    subDirection,
    ref,
    viewport,
    size,
    padding,
    true
  );
}

// fallbacks for each side
const fallbackOrders = {
  bottom: ['bottom', 'top', 'right', 'left'],
  top: ['top', 'bottom', 'right', 'left'],
  left: ['left', 'right', 'bottom', 'top'],
  right: ['right', 'left', 'top', 'bottom'],
};

/**
 * Calculate position for a particular side.
 *
 * @internal
 * @param {string} side Requested side for the popover to display on.
 * @param {?string} secDir Requested position along that side.
 * @param {Rect} ref Rect describing the element to position the popover relative to.
 * @param {Rect} viewport Rect describing the visible part of the document.
 * @param {Size} size Size of the popover.
 * @param {Number} padding Padding around the popover (subtracted from size for some calculations).
 * @param {?bool} force Return result even if it won't fit.
 * @returns {PositionResult}
 */
function calculateOffset(side, secDir, ref, viewport, size, padding, force) {
  const loc = { left: 0, top: 0 };
  let arrowPos = 0;

  // position the primary axis
  switch (side) {
    case 'top':
      loc.top = ref.top - size.height;
      if (loc.top < viewport.top && !force) return null;
      break;
    case 'bottom':
      loc.top = ref.bottom;
      if (loc.top + size.height > viewport.bottom && !force) return null;
      break;
    case 'left':
      loc.left = ref.left - size.width;
      if (loc.left < viewport.left && !force) return null;
      break;
    case 'right':
      loc.left = ref.left + ref.width;
      if (loc.left + size.width > viewport.right && !force) return null;
      break;
  }

  // determine what sides we're looking at to position the secondary axis
  let secSide, secOtherSide, secSize;
  /* istanbul ignore else */
  if (side == 'top' || side == 'bottom') {
    secSide = 'left';
    secOtherSide = 'right';
    secSize = 'width';
  } else if (side == 'left' || side == 'right') {
    secSide = 'top';
    secOtherSide = 'bottom';
    secSize = 'height';
  }

  // if top of popover at its highest possible pos would be below top of ref
  if (viewport[secSide] + padding > ref[secSide] && !force) {
    return null;
  }
  // if bottom of popover at its lowest possible pos would be above bottom of ref
  if (viewport[secOtherSide] - padding < ref[secOtherSide] && !force) {
    return null;
  }

  // position the secondary axis
  if (secDir == secSide) {
    loc[secSide] = ref[secSide] - padding;
  } else if (secDir == secOtherSide) {
    loc[secSide] = ref[secSide] - (size[secSize] - ref[secSize]) + padding;
  } else {
    loc[secSide] = ref[secSide] + (ref[secSize] - size[secSize]) / 2;
  }

  // position the arrow
  arrowPos = ref[secSide] + ref[secSize] / 2;
  if (loc[secSide] < viewport[secSide]) {
    loc[secSide] = viewport[secSide];
  } else if (loc[secSide] + size[secSize] > viewport[secOtherSide]) {
    loc[secSide] = viewport[secOtherSide] - size[secSize];
  }

  return {
    side,
    location: new Point(loc.left, loc.top),
    arrowDistance: arrowPos - loc[secSide] - padding,
  };
}
