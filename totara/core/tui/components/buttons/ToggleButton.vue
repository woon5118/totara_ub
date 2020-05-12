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

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @package totara_core
-->

<template>
  <button
    type="button"
    :class="'tui-toggleBtn' + rtlToggle"
    :aria-pressed="ariaPressed"
    :disabled="disabled"
    @click="togglePressed"
  >
    {{ text }}
    <span class="tui-toggleBtn__ui" aria-hidden="true" />
  </button>
</template>

<script>
import { isRtl } from 'totara_core/i18n';

export default {
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
    text: {
      type: String,
      required: true,
    },
    initialState: {
      default: false,
      type: Boolean,
    },
    value: {
      default: undefined,
      type: Boolean,
    },
  },
  data() {
    return {
      rtlToggle: isRtl() ? ' tui-toggleBtn__rtl' : '',
      state: this.initialState,
    };
  },
  computed: {
    /**
     * Update ariaPressed value based on value or internal state
     *
     * @return {Bool}
     */
    ariaPressed() {
      // If no value prop provided use internal state
      if (this.value === undefined) {
        return this.state;
      }
      return this.value;
    },
  },
  methods: {
    togglePressed() {
      // If no value prop provided toggle internal state
      if (this.value === undefined) {
        this.state = !this.state;
        return;
      }
      // Propagate value change to parent
      this.$emit('input', !this.value);
    },
  },
};
</script>
