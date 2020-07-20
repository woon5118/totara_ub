<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @module totara_core
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
