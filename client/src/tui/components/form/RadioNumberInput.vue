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
  <div class="tui-radioNumberInput">
    <div class="tui-radioNumberInput__number">
      <InputNumber
        :id="$id('numberValue')"
        v-model="numberValue"
        :aria-label="$str('number', 'totara_core')"
        :disabled="disabled"
        @input="update"
      />
    </div>
  </div>
</template>

<script>
// Components
import InputNumber from 'tui/components/form/InputNumber';

export default {
  components: {
    InputNumber,
  },

  props: {
    disabled: Boolean,
    value: [Number, String],
  },

  data() {
    return {
      numberValue: 1,
    };
  },

  computed: {
    selectedValue() {
      return this.numberValue;
    },
  },

  watch: {
    value() {
      this.numberValue = this.value !== undefined ? this.value : 1;
      this.update();
    },
  },

  mounted() {
    this.update();
  },

  methods: {
    /**
     * Update the selected number
     *
     */
    update() {
      this.$emit('accessible-change', this.numberValue);
      this.$emit('input', this.selectedValue);
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "number"
    ]
  }
</lang-strings>
