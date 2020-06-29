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
  <div :class="'tui-toggleBtn' + showToggleFirst">
    <button
      type="button"
      class="tui-toggleBtn__btn"
      :aria-pressed="ariaPressed"
      :aria-label="ariaLabel"
      :disabled="disabled"
      @click="togglePressed"
    >
      <span :class="{ 'sr-only': ariaLabel }">{{ text }}</span>
    </button>

    <div class="tui-toggleBtn__icon">
      <slot name="icon" />
    </div>

    <span
      class="tui-toggleBtn__ui"
      :class="{
        'tui-toggleBtn__ui--aria-pressed': ariaPressed,
      }"
      aria-hidden="true"
      @click="togglePressed"
    />
  </div>
</template>

<script>
export default {
  props: {
    ariaLabel: String,
    disabled: {
      type: Boolean,
      default: false,
    },
    text: {
      type: String,
      required: true,
    },
    initialState: {
      type: Boolean,
      default: false,
    },
    toggleFirst: {
      type: Boolean,
      default: false,
    },
    value: {
      type: Boolean,
      default: undefined,
    },
  },
  data() {
    return {
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

    showToggleFirst() {
      return this.toggleFirst ? ' tui-toggleBtn_left' : ' tui-toggleBtn_right';
    },
  },
  methods: {
    togglePressed() {
      if (this.disabled) return;
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
