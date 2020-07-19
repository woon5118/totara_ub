<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<script>
import { cloneVNode, mergeListeners } from '../../js/internal/vnode';

/**
 * Functional component that modifies vnodes of its children, adding props and
 * event listeners.
 *
 * Note: native elements are ignored (not modified).
 *
 * Use cases:
 *   * Tightly coupled parent->child component relationships, for example
 *     <Radio>s in a <RadioGroup>.
 *   * Applying native listeners to components from renderless components
 *     (e.g. Draggable), until Vue 3 arrives and solves this.
 */
export default {
  functional: true,

  props: {
    provide: {
      type: [Function, Object],
      required: true,
    },
  },

  render(h, { props, children }) {
    return children.map(vnode => {
      vnode = cloneVNode(vnode);
      const opts = vnode.componentOptions;
      if (!opts) return vnode;
      const info = { props: opts.propsData || {} };
      const provided =
        typeof props.provide === 'function'
          ? props.provide(info)
          : props.provide;

      // set provided props...
      if (provided.props) {
        opts.propsData = Object.assign({}, provided.props, opts.propsData);
      }

      if (provided.listeners) {
        opts.listeners = mergeListeners(opts.listeners, provided.listeners);
      }

      if (provided.nativeListeners) {
        // vue event listeners go on `componentOptions.listeners`
        // native listeners go on `data.on`
        // https://github.com/vuejs/vue/blob/b9de23b1008b52deca7e7df40843e318a42f3f53/src/core/vdom/create-component.js#L166
        vnode.data.on = mergeListeners(vnode.data.on, provided.nativeListeners);
      }

      return vnode;
    });
  },
};
</script>
