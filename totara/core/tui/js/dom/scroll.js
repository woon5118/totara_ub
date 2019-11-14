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

/**
 * Measure the width of the browser scrollbar.
 *
 * Note that this will change with zoom level.
 *
 * @returns {number}
 */
export function getScrollbarWidth() {
  const scroller = document.createElement('div');
  Object.assign(scroller.style, {
    position: 'absolute',
    top: '0',
    width: '50px',
    height: '50px',
    overflow: 'scroll',
    visibility: 'hidden',
  });
  document.body.appendChild(scroller);
  const width = scroller.getBoundingClientRect().width - scroller.clientWidth;
  document.body.removeChild(scroller);
  return width;
}

/**
 * Check if the document currently has a scrollbar. Returns false for overlay scrollbars.
 *
 * @returns {boolean}
 */
export function documentHasScrollbar() {
  // checking documentElement.scrollHeight against window.innerHeight does not
  // work in all browsers as some browsers (e.g. IE 11) use overlay scrollbar for
  // the document but regular scrollbars for other elements, instead we check
  // whether the body takes up the full width of the document
  const rect = document.body.getBoundingClientRect();
  // we check rect.left + rect.right instead of rect.width, because the former
  // handles body margin better. rect.width won't handle body margin, but
  // rect.left + rect.right will as long as the margin is the same on each side
  return rect.left + rect.right < window.innerWidth;
}
