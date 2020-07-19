<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <div class="tui-radio">
    <input
      :id="id"
      class="tui-radio__input"
      type="radio"
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
    <label class="tui-radio__label" :for="id">
      <slot />
    </label>
  </div>
</template>

<script>
export default {
  props: {
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
    name: {
      type: String,
      required: true,
    },
    readonly: Boolean,
    required: Boolean,
    // eslint-disable-next-line vue/require-prop-types
    value: {
      required: true,
    },
  },

  methods: {
    handleChange() {
      this.$emit('select', this.value);
    },
  },
};
</script>
