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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @package totara_core
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
