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
const isIE = document.body.classList.contains('ie');

let rootStyle = null;

if (typeof window !== 'undefined') {
  rootStyle = window.getComputedStyle(document.documentElement);
}

const varCache = new Map();
const arrayVarCache = new Map();

export default {
  /**
   * Get the value of a root-level custom property.
   * Omit the leading '--'.
   *
   * @param {string} name
   * @return {?string}
   */
  getVar(name) {
    if (!rootStyle) {
      return;
    }
    if (varCache.has(name)) {
      return varCache.get(name);
    }
    // this code won't execute until styles in head have loaded so it's okay.
    // HTML5 specifies that JS that comes after CSS waits for the CSS to load
    // before executing
    let value = rootStyle.getPropertyValue('--' + name);
    value = value == null || value === '' ? null : value.trim();
    if (value === null && isIE) {
      // can't read custom properties in IE, so they are transformed to have a
      // different name, which we can read by indexing into style
      value = rootStyle['-var--' + name];
      value = value == null || value === '' ? null : value.trim();
    }
    varCache.set(name, value);
    return value;
  },

  /**
   * Get the supported variable usage of a given variable name
   *
   * @param {string} name
   * @return {string}
   */
  getVarUsage(name) {
    return isIE ? this.getVar(name) : `var(--${name})`;
  },

  /**
   * Get the values of a set of sequential root-level custom properties.
   * The properties must be formatted as `--{name}-{index}` where index starts from 1
   * and the rest of the properties must be sequential (no holes).
   *
   * The maximum size of an array is 100 entries.
   *
   * @param {string} name
   * @return {Array<string>}
   */
  getArrayVar(name) {
    if (!rootStyle) {
      return;
    }
    if (arrayVarCache.has(name)) {
      return arrayVarCache.get(name);
    }
    const values = [];
    for (var i = 1; i < 101; i++) {
      const value = this.getVar(name + '-' + i);
      if (value === null) break;
      values.push(value);
    }
    arrayVarCache.set(name, values);
    return values;
  },
};
