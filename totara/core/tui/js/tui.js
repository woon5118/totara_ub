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

import Vue from 'vue';
import VueApollo from 'vue-apollo';
// "import x from 'totara_core/x'" syntax does not work in this file as we're
// too early in the load process, so we must use relative imports
import BundleLoader from './internal/BundleLoader';
import TotaraModuleStore from './internal/TotaraModuleStore';
import tuiPlugin from './tui_vue_plugin';
import i18nPlugin from './i18n_vue_plugin';
import requirements from './internal/requirements';
import { memoize } from './util';
import apolloClient from './apollo_client';
import theme from './theme';

Vue.use(tuiPlugin);
Vue.use(VueApollo);
Vue.use(i18nPlugin);

const hasOwnProperty = Object.prototype.hasOwnProperty;

const loader = new BundleLoader();
const modules = new TotaraModuleStore({ bundleLoader: loader });

const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
});

const emptyComponent = {};

/**
 * TUI core API.
 *
 * This module provides core methods for interacting with TUI - loading modules,
 * mounting components, and using Vue.
 *
 * This is the primary way of interacting with TUI from the outside, provides
 * helpers such as `tui.require()` and `tui.vueAssign()` that are useful when
 * writing Vue components, and provides an interface used by the build tooling.
 */
const tui = {
  /**
   * Vue default export. You can use this to access the Vue export from
   * non-TUI code as `tui.Vue`. In TUI code it is recommended to use the
   * module system instead: `import Vue from 'vue';`
   */
  Vue,

  /**
   * Get all exports of the module with the provided ID.
   *
   * @param {string} id Module ID, e.g.
   *     'totara_core/presentation/Example' or 'vue'.
   * @return {*} Module exports.
   * @throws {Error} when module cannot be found.
   */
  require(id) {
    return modules.require(id);
  },

  /**
   * Asynchronously load the module with the provided ID if it is not loaded,
   * then return all its exports.
   *
   * @param {string} id Module ID, e.g.
   *   'totara_core/presentation/Example' or 'vue'.
   * @returns {Promise}
   *   resolving to module exports, or rejecting when module cannot be found or
   *   bundle load fails.
   */
  import(id) {
    return modules.import(id);
  },

  /**
   * Check if the provided module can be synchronously imported with require().
   *
   * Note that this does not guarantee that the import will succeed, just that
   * we already know if it will succeed or fail (i.e, it's safe to use
   * tui.require() rather than tui.import()).
   *
   * @param {string} id
   */
  syncImportable(id) {
    return modules.syncImportable(id);
  },

  /**
   * Get the default export of the provided module.
   *
   * @param {*} module Module exports.
   * @return {*} Default export.
   * @throws {Error} when module cannot be found.
   */
  defaultExport(module) {
    if (process.env.NODE_ENV !== 'production' && typeof module == 'string') {
      console.warn(
        '[tui] String passed to tui.defaultExport(). ' +
          'Did you mean to call tui.import()/tui.require() first?'
      );
    }
    return modules.default(module);
  },

  /**
   * Get an async component definition for the provided module ID.
   *
   * Can be rendered and will display a loading indicator until the component
   * has loaded.
   *
   * @function
   * @param {string} id
   * @returns {function}
   */
  asyncComponent: memoize(id => {
    return () => ({
      component: tui.loadComponent(id),
      error: tui.defaultExport(
        tui.require('totara_core/components/errors/ErrorPageRender')
      ),
    });
  }),

  /**
   * Load component ready to be rendered.
   *
   * @param {string} id
   * @returns {Promise}
   */
  async loadComponent(id) {
    const comp = modules.default(await modules.import(id));
    await this.loadRequirements(comp);
    return comp;
  },

  /**
   * Mount the specified component over the passed element.
   *
   * Will load the bundle containing the component if it is not loaded.
   *
   * If the component has requirements (e.g. language strings), they will be
   * loaded before the component is mounted.
   *
   * @param {(string|object)} component Component name, e.g. 'totara_core/Example'
   *   or component export.
   * @param {?object} data Component data - see
   * https://vuejs.org/v2/guide/render-function.html#The-Data-Object-In-Depth
   * @param {(Element|string)} el Element to mount at, or selector.
   *   This element will be *replaced* in the DOM.
   * @return {Promise<Vue>} Promise resolving once component is mounted.
   */
  async mount(component, data, el) {
    // warn for incorrect usage
    if (
      process.env.NODE_ENV !== 'production' &&
      typeof component == 'object' &&
      component.__esModule
    ) {
      console.error(
        '[tui] All values exported from a component were passed to ' +
          'tui.mount() - you need to pass a single component only. ' +
          'Try passing through tui.defaultExport().'
      );
    }

    if (typeof component == 'string') {
      component = modules.default(await modules.import(component));
    }

    // load requirements
    if (typeof component == 'function') {
      // async components will load their own requirements
      // still call loadRequirements with a fake component so loading and error
      // pages have strings available
      await this.loadRequirements(emptyComponent);
    } else {
      await this.loadRequirements(component);
    }

    return mount(component, data, el);
  },

  /**
   * Does this component have requirements that need to be loaded before it can
   * be rendered?
   *
   * @return {bool}
   */
  needsRequirements(component) {
    return requirements.get(component).any;
  },

  /**
   * Load data that needs to be loaded before rendering the component and any
   * child components.
   *
   * .mount() automatically does this.
   *
   * .needsRequirements() can be used to synchronously check if a component has any
   * requirements to load.
   *
   * If you are showing components dynamically and they are not present in the
   * `components` object on the parent component, their requirements will not
   * have been picked up automatically when the root component was mounted and
   * you will need to manually call this function before rendering the component.
   *
   * @param {object} component
   * @return {Promise} Promise resolving once requirements have been loaded.
   */
  loadRequirements(component) {
    return requirements.load(requirements.get(component));
  },

  /**
   * Search children of the specified element to find TUI components that need
   * to be initialised.
   *
   * @param {?Element} el Element to search, or document if not specified.
   */
  scan(el) {
    if (!el) {
      el = document;
    }
    const hosts = Array.prototype.slice.call(
      el.querySelectorAll('[data-tui-component]')
    );
    hosts.forEach(function(host) {
      try {
        const component = host.getAttribute('data-tui-component');
        const rawProps = host.getAttribute('data-tui-props');
        const props = rawProps ? JSON.parse(rawProps) : null;
        tui.mount(component, { props: props }, host);
      } catch (e) {
        console.error(e);
      }
    });
  },

  /**
   * Process inheritance for component override.
   *
   * This does not make any changes to the module store.
   * This method is intended to be called from the build system.
   *
   * @private
   * @param {object} component Component export. Component must have been processed by
   *     tui-vue-loader otherwise it will not contain the required
   *     information to evaluate the inheritance.
   * @param {string} parent ID of parent, e.g. 'totara_core/presentation/Example'
   */
  _processOverride(component, parent) {
    if (typeof parent == 'string') {
      let parentName = parent;
      if (modules.isEvaluating(parentName)) {
        parent = modules._requirePrevious(parentName);
        if (!parent) {
          throw new Error(
            'Attempting to override component that does not exist: ' +
              parentName
          );
        }
      } else {
        parent = modules.require(parentName);
      }
      if (parent && parent.__esModule) {
        parent = parent.default;
      }
    }

    // inheritable false: the entire component will be overridden rather than
    // inheriting
    if (parent.inheritable === false) {
      return;
    }

    // components defined with Vue.extend cannot be inherited from
    // it may not be possible to support Vue.extend, as it establishes an
    // inheritance chain
    if (typeof parent == 'function') {
      return;
    }

    if (!component.__hasBlocks) {
      throw new Error(
        'components must be processed by tui-vue-loader to be able to ' +
          'override other components.'
      );
    }

    // inheritance (other than style inheritance) is not allowed when a
    // script block is specified
    if (component.__hasBlocks.script) {
      return;
    }

    // use Vue's built in "extends" option to implement inheritance
    // see: https://vuejs.org/v2/api/#extends
    if (!component.extends) {
      component.extends = parent;
    }
  },

  /**
   * Copy the values from the provided source objects to `target` using
   * Vue.set()
   *
   * @param {*} target
   * @param {...*} constArgs
   */
  vueAssign(target) {
    // based off Object.assign() polyfill from MDN:
    // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
    if (!target) {
      throw new TypeError('Cannot convert undefined or null to object');
    }

    const to = Object(target);

    for (let index = 1; index < arguments.length; index++) {
      const source = arguments[index];

      if (source) {
        for (const key in source) {
          if (hasOwnProperty.call(source, key)) {
            Vue.set(to, key, source[key]);
          }
        }
      }
    }
    return to;
  },

  /**
   * Load the bundles needed for the provided Totara component.
   *
   * @private
   * @param {string} totaraComponent
   * @returns {Promise}
   */
  _loadTotaraComponent(totaraComponent) {
    return loader.loadBundle(totaraComponent);
  },

  /**
   * Get a list of the loaded modules of the specified Totara component.
   *
   * @private
   * @param {string} prefix
   * @returns {string[]}
   */
  _getLoadedComponentModules(totaraComponent) {
    return modules.getLoadedSubmodules(totaraComponent);
  },

  /**
   * Interface to theme
   */
  theme,

  /** @private */
  _modules: modules,

  /** @private */
  _bundle: {
    // proxy module store functions to provide a stable bundle interface:
    /** @private */
    addModulesFromContext: (idBase, context) =>
      modules.addFromContext(idBase, context),
    /** @private */
    addModule: (id, getter) => modules.addModule(id, getter),
    /** @private */
    register: name => loader.registerComponent(name),
    /** @private */
    isLoaded: name => loader.isComponentLoaded(name),
  },
};

/**
 * Internal mount function.
 *
 * Called once we have all information we need to mount the component.
 *
 * @param {object} component
 * @param {?object} data
 * @param {(Element|string)} el
 * @returns {Vue} Vue VM.
 */
function mount(component, data, el) {
  const ErrorBoundary = tui.defaultExport(
    tui.require('totara_core/components/errors/ErrorBoundary')
  );
  const vm = new Vue({
    apolloProvider,
    render(h) {
      return h(ErrorBoundary, {
        scopedSlots: {
          default() {
            return h(component, data);
          },
        },
      });
    },
  });
  vm.$mount(el);
  return vm.$children[0].$children[0];
}

export default tui;
