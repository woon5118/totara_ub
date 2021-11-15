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
 * @module tui
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
