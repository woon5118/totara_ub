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

import apollo from '../apollo_client';
import bundleQuery from 'totara_tui/graphql/bundles_nosession';
import { config } from '../config';
import pending from '../pending';
import { pull } from '../util';

/**
 * Possible states for a bundle
 *
 * @enum
 * @private
 */
const BundleStatus = {
  UNLOADED: undefined,
  LOADING: 1,
  LOADED: 2,
  FAILED: 3,
  NOT_FOUND: 4,
};

/**
 * Load and track bundles for Totara components
 *
 * @private
 */
export default class BundleLoader {
  constructor() {
    this._componentBundleStatus = {};
    this._loadQueue = [];
  }

  /**
   * Register a component bundle as having been loaded
   *
   * @param {string} comp
   */
  registerComponent(comp) {
    this._componentBundleStatus[comp] = BundleStatus.LOADED;
  }

  /**
   * Check if a component bundle has been loaded
   *
   * @param {string} comp
   * @return {boolean}
   */
  isComponentLoaded(comp) {
    return this._componentBundleStatus[comp] == BundleStatus.LOADED;
  }

  /**
   * Determine if component is in final status (loaded or not found)
   *
   * @param {string} status
   * @returns {boolean}
   */
  isComponentFinal(comp) {
    return this._isFinalStatus(comp);
  }

  /**
   * Load the bundles needed for the provided Totara component
   *
   * @param {string} totaraComponent
   * @returns {Promise}
   */
  async loadBundle(totaraComponent) {
    if (this.isComponentLoaded(totaraComponent)) {
      return Promise.resolve();
    }
    const result = await this._loadBundles([totaraComponent]);
    result.forEach(bundleResult => {
      if (bundleResult.error == 'error') {
        throw new Error(bundleResult.error);
      }
    });
  }

  /**
   * Load the provided bundles
   *
   * @private
   * @param {string[]} totaraComponents
   * @returns {object[]}
   */
  async _loadBundles(totaraComponents) {
    // queue loads so we don't double up
    return this._queueLoadTask(() => this._loadBundles_task(totaraComponents));
  }

  /**
   * Implementation for _loadBundles
   *
   * @private
   * @param {string[]} totaraComponents
   * @returns {object[]}
   */
  async _loadBundles_task(totaraComponents) {
    // filter request to bundles that we don't already have
    totaraComponents = totaraComponents.filter(x =>
      this._isLoadableStatus(this._componentBundleStatus[x.component])
    );

    if (totaraComponents.length == 0) {
      return [];
    }

    // TODO: could potentially cache this in localstorage to improve performance
    // (after taking into account config.rev.js value and theme)
    const result = await apollo.query({
      query: bundleQuery,
      variables: {
        components: totaraComponents,
        theme: config.theme.name,
      },
      fetchPolicy: 'no-cache',
    });

    // filter result to bundles that we don't already have
    const bundles = result.data.bundles.filter(x =>
      this._isLoadableStatus(this._componentBundleStatus[x.component])
    );
    // mark them as loading
    bundles.forEach(bundle => {
      this._componentBundleStatus[bundle.component] = BundleStatus.LOADING;
    });

    // find bundles we want but did not get back in response
    const missing = totaraComponents.filter(
      x => !result.data.bundles.some(y => y.component == x)
    );
    // mark as not found so we don't keep requesting them from the server
    missing.forEach(
      comp => (this._componentBundleStatus[comp] = BundleStatus.NOT_FOUND)
    );

    return Promise.all(
      bundles
        .map(async bundle => {
          try {
            await this._loadBundleDefinition(bundle);
            if (bundle.type == 'js') {
              this._componentBundleStatus[bundle.component] =
                BundleStatus.LOADED;
            }
            return { status: 'success' };
          } catch (e) {
            if (bundle.type == 'js') {
              this._componentBundleStatus[bundle.component] =
                BundleStatus.FAILED;
            }
            return {
              status: 'error',
              error: `Unable to load bundle "${bundle.name}" for "${bundle.component}"`,
            };
          }
        })
        // report missing bundles
        .concat(
          missing.map(x => ({
            status: 'error',
            error: `Unable to find a TUI bundle for "${x}"`,
          }))
        )
    );
  }

  /**
   * Load a bundle from a bundle definition
   *
   * @private
   * @param {object} bundle
   * @returns {Promise}
   */
  _loadBundleDefinition(bundle) {
    switch (bundle.type) {
      case 'js':
        return this._loadScript(bundle.url);
      case 'css':
        return this._loadStyle(bundle.url);
      default:
        throw new Error('Unknown bundle type');
    }
  }

  /**
   * Load the script at the specified URL.
   *
   * @private
   * @param {string} url
   * @returns {Promise} resolving if script loads and rejecting if it fails
   */
  _loadScript(url) {
    return new Promise((resolve, reject) => {
      const done = pending('script-loading');
      const script = document.createElement('script');
      script.src = url;
      script.addEventListener('load', () => {
        script.remove();
        done();
        resolve();
      });
      script.addEventListener('error', () => {
        script.remove();
        done();
        reject();
      });
      document.head.appendChild(script);
    });
  }

  /**
   * Load the stylesheet at the specified URL.
   *
   * @private
   * @param {string} url
   * @returns {Promise} resolving if stylesheet loads and rejecting if it fails
   */
  _loadStyle(url) {
    return new Promise((resolve, reject) => {
      const done = pending('styles-loading');
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = url;
      link.addEventListener('load', () => {
        done();
        return resolve();
      });
      link.addEventListener('error', () => {
        done();
        return reject();
      });

      // last tui_scss link should be the theme css, so insert directly before that.
      const tuiLinks = document.head.querySelectorAll(
        'link[rel=stylesheet][href*=tui_scss]'
      );
      const lastTuiLink = tuiLinks[tuiLinks.length - 1];
      if (lastTuiLink) {
        lastTuiLink.parentNode.insertBefore(link, lastTuiLink);
      } else {
        document.head.appendChild(link);
      }
    });
  }

  /**
   * Add a task to the load queue.
   *
   * @private
   * @param {function} fn
   * @returns {Promise}
   *   resolving or rejecting when the result of fn() resolves or rejects
   */
  _queueLoadTask(fn) {
    return new Promise((resolve, reject) => {
      this._loadQueue.push(() => {
        return Promise.resolve(fn()).then(resolve, reject);
      });
      // only item in queue, begin
      if (this._loadQueue.length == 1) {
        this._nextLoadTask();
      }
    });
  }

  /**
   * Execute the next task in the load queue. Should not be called except by _queueLoadTask.
   *
   * @private
   */
  _nextLoadTask() {
    if (this._loadQueue.length == 0) {
      return;
    }
    const task = this._loadQueue[0];
    task().then(() => {
      pull(this._loadQueue, task);
      this._nextLoadTask();
    });
  }

  /**
   * Determine if bundle is in final status (loaded or not found)
   *
   * @private
   * @param {*} status
   * @returns {boolean}
   */
  _isFinalStatus(status) {
    return status == BundleStatus.LOADED || status == BundleStatus.NOT_FOUND;
  }

  /**
   * Determine if bundle is in loadable status (unloaded or failed)
   *
   * @private
   * @param {*} status
   * @returns {boolean}
   */
  _isLoadableStatus(status) {
    return status == BundleStatus.UNLOADED || status == BundleStatus.FAILED;
  }
}
