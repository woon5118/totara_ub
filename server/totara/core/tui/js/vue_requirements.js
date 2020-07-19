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

import { memoizeWeak } from './util';

/**
 * Create a funtion that will recursively scan a Vue component and its
 * subcomponents to extract values.
 *
 * @param {object} options
 * @param {function} options.extract
 *     Function that takes a component definition and returns data to extract.
 * @param {?boolean} options.cache
 *     Cache the result of the computation for each component?
 * @param {function} options.mergeSubresult
 *     Called with result and subresult for each subcomponent, and returns
 *     processed result.
 *     OK to mutate result in this function.
 * @param {?function} options.postprocess
 *     Called to process result before it is cached/returned.
 *     Takes result and returns processed result.
 *     OK to mutate result in this function.
 * @return {function}
 */
export function makeScanner(options) {
  let scan = component => {
    // handle components defined with Vue.extend()
    if (component.options) {
      component = component.options;
    }

    // extract value from component
    let result = options.extract(component);

    // scan component we are extending
    if (component.extends) {
      result = options.mergeSubresult(result, scan(component.extends));
    }

    // scan child components
    if (component.components) {
      for (const key in component.components) {
        if (!Object.prototype.hasOwnProperty.call(component.components, key)) {
          continue;
        }
        const comp = component.components[key];
        if (comp) {
          result = options.mergeSubresult(result, scan(comp));
        }
      }
    }

    // postprocess - used to deduplicate etc
    if (options.postprocess) {
      result = options.postprocess(result);
    }

    return result;
  };

  if (options.cache) {
    // assign over original reference so recursive usage uses the memoized
    // version of the function too
    scan = memoizeWeak(scan);
  }

  return scan;
}

/**
 * Get the specified option from a component, checking the components it
 * extends from too and merging according to the specified strategy.
 *
 * @param {object} component
 * @param {string} name
 * @param {function=} mergeStrategy
 * @return {*}
 */
export function getInheritedOption(component, name, mergeStrategy) {
  // short circuit:
  // if the component doesn't extend anything don't bother with merging logic
  // etc, just return the value
  if (!component.extends) {
    return component[name];
  }

  const parentValue = getInheritedOption(
    component.extends,
    name,
    mergeStrategy
  );

  const currentValue = component[name];

  if (mergeStrategy) {
    return mergeStrategy(parentValue, currentValue);
  } else {
    // default merge strategy - use current value if exists, otherwise use
    // parent value
    return currentValue !== undefined ? currentValue : parentValue;
  }
}
