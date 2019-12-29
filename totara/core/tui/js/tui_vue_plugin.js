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
import { uniqueId, url } from './util';
import { trapFocusOnTab } from './dom/focus';

const hasOwnProperty = Object.prototype.hasOwnProperty;

export default {
  install(Vue) {
    Object.defineProperties(Vue.prototype, {
      /**
       * Get a unique ID for the current component instance
       */
      uid: {
        get() {
          // lazily generate unique ids when a component needs one
          return this.$_tui_uid || (this.$_tui_uid = 'uid-' + uniqueId());
        },
        enumerable: true,
        configurable: true,
      },
    });

    /**
     * Get a globally unique ID string scoped to within this component.
     *
     * Equivalent to this.uid + '-' + id.
     *
     * @param {string=} id
     */
    Vue.prototype.$id = function(id) {
      return id ? this.uid + '-' + id : this.uid;
    };

    /**
     * Get a reference (#) to an ID string.
     *
     * Prepends # to the result of $id().
     *
     * @param {string=} id
     */
    Vue.prototype.$idRef = function(id) {
      return '#' + this.$id(id);
    };

    Vue.prototype.$url = url;

    // expose window as $window to avoid using a global
    /* istanbul ignore else */
    if (typeof window !== 'undefined') {
      Vue.prototype.$window = window;
    }

    // trap focus inside the specified element
    const trapFocusHandlers = new WeakMap();
    Vue.directive('trap-focus', {
      inserted(el) {
        const handler = e => {
          if (e.key == 'Tab') {
            trapFocusOnTab(el, e);
          }
        };
        document.addEventListener('keydown', handler);
        trapFocusHandlers.set(el, handler);
      },

      unbind(el) {
        const handler = trapFocusHandlers.get(el);
        document.removeEventListener('keydown', handler);
        trapFocusHandlers.delete(el);
      },
    });

    // toggle class if inner node has focus
    const focusHandlers = new WeakMap();
    const focusHandlerClass = 'tui-focusWithin';
    Vue.directive('focus-within', {
      bind(el, binding) {
        if (hasOwnProperty.call(binding, 'value') && !binding.value) {
          return;
        }
        let hasFocusWithin = false;
        const inHandler = () => {
          el.classList.add(focusHandlerClass);
          focusHandlers.get(el).hasFocusWithin = true;
        };
        const outHandler = () => {
          el.classList.remove(focusHandlerClass);
          focusHandlers.get(el).hasFocusWithin = false;
        };

        el.addEventListener('focusin', inHandler);
        el.addEventListener('focusout', outHandler);
        focusHandlers.set(el, { inHandler, outHandler, hasFocusWithin });
      },

      // Class is removed when updating component, readd if it was active
      componentUpdated: function(el) {
        if (!focusHandlers.has(el)) {
          return;
        }
        if (focusHandlers.get(el).hasFocusWithin) {
          el.classList.add(focusHandlerClass);
        }
      },

      unbind(el) {
        if (!focusHandlers.has(el)) {
          return;
        }
        const elEvents = focusHandlers.get(el);
        el.removeEventListener('focusin', elEvents.inHandler);
        el.removeEventListener('focusout', elEvents.outHandler);
        focusHandlers.delete(el);
      },
    });
  },
};
