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
  @module tui
-->

<template>
  <div
    class="tui-checkbox"
    :class="{
      'tui-checkbox--large': large,
    }"
  >
    <input
      :id="id"
      class="tui-checkbox__input"
      type="checkbox"
      :aria-describedby="ariaDescribedby"
      :aria-label="ariaLabel"
      :aria-labelledby="ariaLabelledby"
      :autocomplete="autocomplete"
      :autofocus="autofocus"
      :checked="checked"
      :disabled="disabled"
      :name="name"
      :readonly="readonly"
      :required="required"
      :value="value"
      @change="handleChange"
    />
    <label
      class="tui-checkbox__label"
      :class="{
        'tui-checkbox__label--noOffset': noLabelOffset,
      }"
      :for="id"
    >
      <slot />
    </label>
  </div>
</template>

<script>
export default {
  model: {
    prop: 'checked',
    event: 'change',
  },

  props: {
    ariaDescribedby: String,
    ariaLabel: String,
    ariaLabelledby: String,
    autocomplete: Boolean,
    autofocus: Boolean,
    checked: Boolean,
    disabled: Boolean,
    id: {
      type: String,
      default() {
        return this.uid;
      },
    },
    large: Boolean,
    name: String,
    noLabelOffset: Boolean,
    readonly: Boolean,
    required: Boolean,
    value: String,
  },

  methods: {
    handleChange(e) {
      this.$emit('change', e.target.checked);
    },
  },
};
</script>
