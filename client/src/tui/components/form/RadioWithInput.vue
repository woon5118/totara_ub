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
  @module totara_core
-->

<template>
  <div class="tui-radioWithInput">
    <Radio
      :name="groupName || name"
      :value="value"
      :aria-label="accessibleValue"
      :checked="checked"
      :disabled="disabled"
      class="tui-radioWithInput__radio"
      @select="$emit('select', $event)"
    >
      {{ text }}
    </Radio>
    <slot
      :disabledRadio="disabled || !checked"
      :setAccessibleLabel="setAccessibleLabel"
      :update="update"
      :value="controlValue"
    />
  </div>
</template>

<script>
// Components
import Radio from 'tui/components/form/Radio';

export default {
  components: {
    Radio,
  },

  model: {
    prop: 'controlValue',
    event: 'input',
  },

  props: {
    checked: Boolean,
    // eslint-disable-next-line vue/require-prop-types
    controlValue: {},
    disabled: Boolean,
    groupName: String,
    name: {
      type: String,
    },
    text: String,
    value: [Array, Boolean, Number, String],
  },

  data() {
    return {
      accessibleValue: '',
    };
  },

  methods: {
    /**
     * Update the values from sub inputs
     *
     */
    update(value) {
      this.$emit('input', value);
    },

    /**
     * Update the accessibility label
     *
     */
    setAccessibleLabel(value) {
      this.accessibleValue = value;
    },
  },
};
</script>
