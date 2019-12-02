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

import { Point, Rect, Size } from 'totara_core/geometry';

/**
 * Get the position of an element relative to the document.
 *
 * @param {HTMLElement} element
 * @returns {Point}
 */
export function getDocumentPosition(element) {
  let x = 0;
  let y = 0;
  let el = element;

  while (el) {
    x += el.offsetLeft || 0;
    y += el.offsetTop || 0;
    el = el.offsetParent;
  }

  el = element;

  // account for scrolling of elements
  // do not account for document scrolling
  while (el && el !== document.documentElement) {
    x -= el.scrollLeft || 0;
    y -= el.scrollTop || 0;
    el = el.parentNode;
  }

  return new Point(x, y);
}

/**
 * Get a Rect representing the element's size and position relative to its offset parent.
 *
 * @param {HTMLElement} el
 * @returns {Rect}
 */
export function getOffsetRect(el) {
  return new Rect(el.offsetLeft, el.offsetTop, el.offsetWidth, el.offsetHeight);
}

/**
 * Get a Rect representing the currently visbile portion of the document.
 *
 * @returns {Rect}
 */
export function getViewportRect() {
  const html = document.documentElement;
  return new Rect(
    window.pageXOffset,
    window.pageYOffset,
    html.clientWidth,
    html.clientHeight
  );
}

/**
 * Get box model information.
 *
 * @param {Element} el
 * @param {Object} [options]
 * @param {boolean} [options.transformed=false] Get the position post-transform? (default false)
 * @param {boolean} [options.viewport=false] Get the position relative to the viewport? (default false)
 * @returns {{ marginBox: Rect, borderBox: Rect }}
 */
export function getBox(el, { transformed = false, viewport = false } = {}) {
  let position;
  let size;
  if (transformed) {
    const bcr = el.getBoundingClientRect();
    position = new Point(bcr.x, bcr.y);
    if (!viewport) {
      position = position.add(getViewportRect().getPosition());
    }
    size = new Size(bcr.width, bcr.height);
  } else {
    position = getDocumentPosition(el);
    if (viewport) {
      position = position.sub(getViewportRect().getPosition());
    }
    size = new Size(el.offsetWidth, el.offsetHeight);
  }
  const borderBox = new Rect(position.x, position.y, size.width, size.height);
  const style = getComputedStyle(el);

  const margin = {
    top: parseSize(style.marginTop),
    right: parseSize(style.marginRight),
    bottom: parseSize(style.marginBottom),
    left: parseSize(style.marginLeft),
  };

  return {
    marginBox: alterRect(borderBox, margin, 1),
    borderBox,
    margin,
  };
}

/**
 * Alter Rect by adding or removing spacing on each side.
 *
 * @param {Rect} rect
 * @param {{ top: number, right: number, bottom: number, left: number }} spacing
 * @param {number} multiplier 1 - expand, -1 - shrink
 */
function alterRect(rect, spacing, multiplier) {
  return Rect.fromPositions({
    top: rect.top - spacing.top * multiplier,
    right: rect.right + spacing.right * multiplier,
    bottom: rect.bottom + spacing.bottom * multiplier,
    left: rect.left - spacing.left * multiplier,
  });
}

/**
 * Parse CSS size value, e.g. from marginLeft.
 *
 * @param {string} str
 * @returns {number}
 */
function parseSize(str) {
  // computed styles are in px when visible, original values otherwise.
  // if it's not visible, treat everything as 0
  return str.slice(-2) == 'px' ? Number(str.slice(0, -2)) : 0;
}
