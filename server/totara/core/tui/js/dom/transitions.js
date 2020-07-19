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

import pending from '../pending';

/**
 * Get the smallest non-zero transition duration in ms specified for the provided element, or 0 if none
 *
 * @param {Element} element
 * @returns {number} Transition duration in milliseconds. Will be 0 if there is none specified.
 */
function getSmallestTransitionDuration(element) {
  const styles = window.getComputedStyle(element);
  const durations = styles.transitionDuration
    .split(',')
    .map(getMilliseconds)
    .filter(x => x > 0);
  if (durations.length == 0) {
    return 0;
  }
  return Math.min(...durations);
}

/**
 * Get value in milliseconds from transition duration value (1.2s, 200ms), or 0 if unknown.
 *
 * @param {string} value
 * @returns {number}
 */
function getMilliseconds(value) {
  value = value.trim();
  let num = parseFloat(value);
  if (isNaN(num)) {
    return 0;
  }
  if (value.endsWith('ms')) {
    return num;
  } else if (value.endsWith('s')) {
    return num * 1000;
  } else {
    return 0;
  }
}

/**
 * Wait for transition(s) to end, with a backup timeout in case the event is not fired
 *
 * @param {Element|Element[]} element DOM element, or array of DOM elements
 * @returns {Promise}
 */
export function waitForTransitionEnd(element) {
  if (Array.isArray(element)) {
    return Promise.all(element.map(x => waitForTransitionEnd(x)));
  }
  const done = pending('transition-end');
  return new Promise(resolve => {
    function handler() {
      element.removeEventListener('transitionend', handler);
      done();
      resolve();
    }
    element.addEventListener('transitionend', handler);
    const duration = getSmallestTransitionDuration(element);
    setTimeout(handler, duration > 0 ? duration + 800 : 0);
  });
}
