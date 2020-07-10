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
  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package totara_core
-->

<template>
  <div
    :class="{
      'tui-toggleSwitch': true,
      'tui-toggleSwitch--left': toggleFirst,
    }"
  >
    <button
      :id="id"
      type="button"
      class="tui-toggleSwitch__btn"
      :aria-label="ariaLabel"
      :aria-pressed="value"
      :disabled="disabled"
      @click="togglePressed"
    >
      <span :class="{ 'sr-only': ariaLabel }">{{ text }}</span>
    </button>

    <div class="tui-toggleSwitch__icon">
      <slot name="icon" />
    </div>

    <span
      class="tui-toggleSwitch__ui"
      :class="{
        'tui-toggleSwitch__ui--aria-pressed': value,
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
    id: {
      type: String,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    text: {
      type: String,
    },
    toggleFirst: {
      type: Boolean,
      default: false,
    },
    value: {
      type: Boolean,
    },
  },
  methods: {
    togglePressed() {
      if (this.disabled) return;
      // Propagate value change to parent
      this.$emit('input', !this.value);
    },
  },
};
</script>
