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
    class="tui-select"
    :class="{
      'tui-select--disabled': disabled,
      'tui-select--large': large,
      'tui-select--multiple': multiple,
    }"
  >
    <select
      :id="id"
      v-model="selectedValue"
      class="tui-select__input"
      :aria-label="ariaLabel"
      :aria-labelledby="ariaLabelledby"
      :autocomplete="autocomplete"
      :autofocus="autofocus"
      :disabled="disabled"
      :multiple="multiple"
      :name="name"
      :required="required"
      :size="size"
    >
      <template v-for="(option, i) in normalizedOptions">
        <optgroup v-if="option.options" :key="i" :label="option.label">
          <option
            v-for="(suboption, j) in option.options"
            :key="j"
            :value="suboption.id"
            :disabled="suboption.disabled"
          >
            {{ suboption.label }}
          </option>
        </optgroup>
        <option v-else :key="i" :value="option.id" :disabled="option.disabled">
          {{ option.label }}
        </option>
      </template>
    </select>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,

  props: {
    id: String,
    ariaLabel: [Boolean, String],
    ariaLabelledby: String,
    autocomplete: String,
    autofocus: Boolean,
    disabled: Boolean,
    large: Boolean,
    multiple: Boolean,
    name: String,
    options: {
      type: [Array, Object],
      required: true,
    },
    required: Boolean,
    size: [Number, String],
    // eslint-disable-next-line vue/require-prop-types
    value: {},
  },

  computed: {
    normalizedOptions() {
      return this.options.map(this.$_normalizeOption);
    },

    selectedValue: {
      get() {
        return this.value;
      },

      set(value) {
        this.$emit('input', value);
      },
    },
  },

  methods: {
    $_normalizeOption(option) {
      if (typeof option === 'string') {
        option = { label: option, id: option };
      }
      if (option.options) {
        option = Object.assign({}, option, {
          options: option.options.map(this.$_normalizeOption),
        });
      }
      return option;
    },
  },
};
</script>
