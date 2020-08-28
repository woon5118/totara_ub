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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module tui
-->

<template>
  <button
    ref="button"
    class="tui-toggleBtn"
    :class="{
      'tui-toggleBtn--selected': selected,
      'tui-toggleBtn--hasText': text && !large,
      'tui-toggleBtn--large': large,
    }"
    :aria-checked="selected.toString()"
    :aria-label="ariaLabel"
    :disabled="disabled"
    :name="name"
    role="radio"
    :tabindex="selected ? null : -1"
    type="button"
    :value="value"
    @click="toggleState"
    @keydown="$emit('keydown', $event)"
  >
    <div class="tui-toggleBtn__content" aria-hidden="true">
      <span v-if="$scopedSlots.default" class="tui-toggleBtn__icon">
        <slot />
      </span>
      <span v-if="text" class="tui-toggleBtn__text">
        {{ text }}
      </span>
    </div>
  </button>
</template>

<script>
export default {
  props: {
    ariaLabel: {
      type: String,
      required: true,
    },
    disabled: Boolean,
    large: Boolean,
    name: String,
    selected: {
      type: Boolean,
      default: false,
    },
    text: String,
    title: String,
    value: [Boolean, String],
  },

  methods: {
    /**
     * Set focus to the button on click (doesn't happen on Firefox on OSX)
     */
    setFocus() {
      this.$refs.button.focus();
    },

    /**
     * Emit button click event
     */
    toggleState() {
      // If already selected, don't emit clicked event
      if (this.selected) {
        return;
      }
      this.setFocus();
      this.$emit('clicked', this.value);
    },
  },
};
</script>
