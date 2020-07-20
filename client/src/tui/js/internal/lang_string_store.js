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

import { cacheGet, cacheSet } from './persistent_cache';
import apollo from '../apollo_client';
import langStringQuery from 'core/graphql/lang_strings_nosession';
import { config } from '../config';

const loadedStrings = {};
const loadingPromises = {};

/**
 * Get lang string.
 *
 * @param {string} key
 * @param {string} comp
 * @returns {string}
 */
export function getString(key, comp) {
  return loadedStrings[`${comp}/${key}`];
}

/**
 * Check if we have loaded lang string.
 *
 * @param {string} key
 * @param {string} comp
 * @returns {boolean}
 */
export function hasString(key, comp) {
  return !!loadedStrings[`${comp}/${key}`];
}

/**
 * Set the lang string in the map of loaded strings and the cache.
 *
 * @param {string} key
 * @param {string} comp
 * @param {string} value
 */
function setString(key, comp, value) {
  const stringKey = `${comp}/${key}`;
  loadedStrings[stringKey] = value;
  const lang = config.locale.language;
  const cacheKey = `core_str/${lang}/${stringKey}`;
  cacheSet(cacheKey, value);
}

/**
 * Load all of the specified strings so that they are available to use.
 *
 * @param {array} requests Array of format [{ component: 'foo', key: 'bar' }]
 */
export function loadStrings(reqs) {
  const waitingFor = [];
  const needed = [];

  const lang = config.locale.language;

  reqs.forEach(req => {
    const stringKey = `${req.component}/${req.key}`;
    if (loadedStrings[stringKey]) return;
    const cacheKey = `core_str/${lang}/${stringKey}`;
    const cached = cacheGet(cacheKey);
    if (cached) {
      loadedStrings[stringKey] = cached;
      return;
    }

    if (loadingPromises[stringKey]) {
      if (!waitingFor.includes(loadingPromises[stringKey])) {
        waitingFor.push(loadingPromises[stringKey]);
      }
      return;
    }

    needed.push(req);
  });

  if (needed.length > 0) {
    const promise = apollo
      .query({
        query: langStringQuery,
        variables: {
          lang,
          ids: needed.map(x => `${x.key}, ${x.component}`),
        },
        fetchPolicy: 'no-cache',
      })
      .then(result => {
        result.data.lang_strings.forEach(item => {
          setString(item.identifier, item.component, item.string);
        });
      });
    waitingFor.push(promise);
  }

  return Promise.all(waitingFor);
}
