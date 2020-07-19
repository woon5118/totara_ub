<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

<template>
  <div
    class="tui-checkboxGroup"
    :class="{ 'tui-checkboxGroup--horizontal': horizontal }"
    role="group"
    :aria-labelledby="ariaLabelledby"
  >
    <PropsProvider :provide="provide">
      <slot />
    </PropsProvider>
  </div>
</template>

<script>
import PropsProvider from 'totara_core/components/util/PropsProvider';

export default {
  components: {
    PropsProvider,
  },

  props: {
    ariaLabelledby: String,
    disabled: Boolean,
    horizontal: Boolean,
    name: {
      type: String,
      default() {
        return this.uid;
      },
    },
    value: [Array, Object],
    /**
     * Format of value - if false, it is an array of values.
     * If true, it is an object map of value -> boolean.
     */
    useObject: Boolean,
  },

  methods: {
    provide({ props }) {
      return {
        props: {
          name: this.name,
          checked: this.useObject
            ? this.value && this.value[props.value]
            : Array.isArray(this.value) && this.value.includes(props.value),
          disabled: this.disabled,
        },
        listeners: {
          change: checked => {
            let newValue;
            if (this.useObject) {
              newValue = Object.assign({}, this.value);
              newValue[props.value] = checked;
            } else {
              newValue = Array.isArray(this.value)
                ? this.value.filter(x => x !== props.value)
                : [];
              if (checked) {
                newValue.push(props.value);
              }
            }
            this.$emit('input', newValue);
          },
        },
      };
    },
  },
};
</script>
