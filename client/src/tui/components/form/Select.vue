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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
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
      :aria-describedby="ariaDescribedby"
      :aria-invalid="ariaInvalid"
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
    ariaDescribedby: [String, Boolean],
    ariaInvalid: [String, Boolean],
    ariaLabel: [String, Boolean],
    ariaLabelledby: [String, Boolean],
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
