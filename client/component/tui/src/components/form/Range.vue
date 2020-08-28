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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module tui
-->

<template>
  <div class="tui-range">
    <!-- Labels -->
    <div class="tui-range__labels">
      <div class="tui-range__lowLabel">{{ rangeLowLabel }}</div>
      <div class="tui-range__highLabel">{{ rangeHighLabel }}</div>
    </div>

    <!-- Slider -->
    <input
      :id="id"
      :class="['tui-range__input', value && 'tui-range__input--selected']"
      type="range"
      :aria-label="ariaLabel"
      :aria-labelledby="ariaLabelledby"
      :autocomplete="autocomplete"
      :autofocus="autofocus"
      :disabled="disabled"
      :name="name"
      :readonly="readonly"
      :required="required"
      :value="value || defaultValue"
      :min="min"
      :max="max"
      :step="step"
      @input="handleChange"
      @change="handleChange"
      @click="handleChange"
      @focus="handleChange"
    />
  </div>
</template>

<script>
export default {
  model: {
    prop: 'value',
    event: 'change',
  },

  props: {
    id: {
      type: String,
      default() {
        return this.uid;
      },
    },
    ariaLabel: String,
    ariaLabelledby: String,
    autocomplete: Boolean,
    autofocus: Boolean,
    disabled: Boolean,
    name: String,
    readonly: Boolean,
    required: Boolean,
    value: [Number, String],
    defaultValue: [Number, String],
    min: [Number, String],
    max: [Number, String],
    step: [Number, String],
    showLabels: Boolean,
    lowLabel: String,
    highLabel: String,
  },

  computed: {
    rangeLowLabel() {
      return this.showLabels ? this.lowLabel : this.min;
    },
    rangeHighLabel() {
      return this.showLabels ? this.highLabel : this.max;
    },
  },

  methods: {
    /**
     * Trigger an event notifying the parent of a change in the range's value.
     * Also caters for initial click events selecting the default value as this
     * does not execute the input/change events. Dragging the thumb around will
     * only emit the @input event.
     *
     * @param e
     */
    handleChange(e) {
      const value = e.target.value;
      if (value !== this.value) {
        this.$emit('change', value);
      }
    },
  },
};
</script>
