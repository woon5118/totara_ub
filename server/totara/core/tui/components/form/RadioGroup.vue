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

<template>
  <div
    class="tui-radioGroup"
    :class="{ 'tui-radioGroup--horizontal': horizontal }"
    role="radiogroup"
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
    required: Boolean,
    value: [Array, Boolean, Number, String],
  },

  methods: {
    provide({ props }) {
      return {
        props: {
          name: this.name,
          checked: props.value == this.value,
          disabled: this.disabled,
          required: this.required,
        },
        listeners: {
          select: this.$_handleSelect,
        },
      };
    },

    $_handleSelect(value) {
      this.$emit('input', value);
    },
  },
};
</script>
