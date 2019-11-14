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

const TABBABLE_ELEMENT_SELECTOR = [
  'a[href]',
  'area[href]',
  'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',
  'select:not([disabled]):not([aria-hidden])',
  'textarea:not([disabled]):not([aria-hidden])',
  'button:not([disabled]):not([aria-hidden])',
  'iframe',
  'object',
  'embed',
  '[contenteditable]',
  // negative tabindex is not tabbable (but is focusable)
  '[tabindex]:not([tabindex^="-"])',
].join(', ');

/**
 * Handle tab key press and keep focus within the provided element
 *
 * @param {Element} el
 * @param {KeyboardEvent} e
 */
export function trapFocusOnTab(el, e) {
  if (e.key != 'Tab') {
    return;
  }
  const elements = getTabbableElements(el);
  if (elements.length === 0) {
    return;
  }

  if (!el.contains(document.activeElement)) {
    elements[0].focus();
  } else {
    const index = elements.indexOf(document.activeElement);

    // if we're tabbing off the edge of the elements array, loop around
    if (!e.shiftKey && index === elements.length - 1) {
      elements[0].focus();
      e.preventDefault();
    }
    if (e.shiftKey && index === 0) {
      elements[elements.length - 1].focus();
      e.preventDefault();
    }
  }
}

/**
 * Check if the specified element is visible for focus purposes
 *
 * @param {Element} el
 */
function visible(el) {
  const computedStyle = getComputedStyle(el);
  return (
    computedStyle.display !== 'none' &&
    computedStyle.visibility !== 'hidden' &&
    !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length)
  );
}

/**
 * Get tabbable elements within the provided element
 *
 * @param {Element} el
 */
export function getTabbableElements(el) {
  const nodes = el.querySelectorAll(TABBABLE_ELEMENT_SELECTOR);
  return Array.prototype.slice.apply(nodes).filter(x => visible(x));
}
