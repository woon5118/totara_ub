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

import Vue from 'vue';

import { baseSet } from './internal/util/object';

function getOptions(component) {
  return Vue.extend(component).options;
}

/**
 * Get props from Vue component definition.
 *
 * @param {*} component Vue component definition.
 * @returns {object}
 */
export function getPropDefs(component) {
  return getOptions(component).props;
}

/**
 * Get `model: { prop, event }` setting from Vue component definition.
 *
 * @param {*} component Vue component definition.
 * @returns {object}
 */
export function getModelDef(component) {
  return getOptions(component).model;
}

/**
 * Reactively set the value at path of object.
 *
 * Arrays are created for missing index properties and objects are created for other missing properties.
 *
 * @param {object} object Object to modify.
 * @param {array} path Path of property to set, e.g. ['a', 2, 'q']
 * @param {*} value
 * @returns {object}
 */
export function set(object, path, value) {
  baseSet(object, path, value, Vue.set);
}

const defaultPropEqual = (val, old) => val == old;

/**
 * Create a Vue watcher that warns when prop is changed.
 *
 * @param {string} component
 * @param {string} prop
 * @param {(val, old) => boolean} propEqual Function to check if prop has the same value.
 * @returns {(val, old) => void}
 */
export const createImmutablePropWatcher = (
  component,
  prop,
  propEqual = defaultPropEqual
) => (val, old) => {
  if (!propEqual(val, old)) {
    console.error(
      `[${component}] Prop "${prop}" has changed from ${JSON.stringify(old)} ` +
        `to ${JSON.stringify(val)}. This change will not be reflected. ` +
        `Instead, recreate the ${component} component instance with the ` +
        `new prop value. ` +
        `(change the "key" prop to force a new instance to be created)`
    );
  }
};
