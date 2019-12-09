<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-toggleSet">
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
    disabled: Boolean,
    value: String,
  },

  methods: {
    /**
     * Provide disabled & selected props to inner toggle buttons
     *
     * @param {string} selected
     */
    provide({ props }) {
      return {
        props: {
          disabled: this.disabled,
          selected: props.value == this.value,
        },
        listeners: {
          clicked: this.$_handleSelect,
        },
      };
    },

    /**
     * Toggle selected button
     *
     * @param {string} selected
     */
    $_handleSelect(selected) {
      this.$emit('input', selected);
    },
  },
};
</script>
