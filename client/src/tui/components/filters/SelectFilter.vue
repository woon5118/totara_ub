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
  <div
    class="tui-selectFilter"
    :class="{ 'tui-selectFilter--stacked': stacked }"
  >
    <Label
      v-if="!dropLabel"
      :for-id="generatedId"
      :hidden="!showLabel"
      :label="label"
    />
    <Select
      :id="generatedId"
      v-bind="$props"
      :aria-label="ariaLabel"
      @input="input"
    />
  </div>
</template>

<script>
// Components
import Label from 'tui/components/form/Label';
import Select from 'tui/components/form/Select';

export default {
  components: {
    Label,
    Select,
  },
  inheritAttrs: false,

  /* eslint-disable vue/require-prop-types */
  props: {
    ariaDescribedby: {},
    ariaLabelledby: {},
    autocomplete: {},
    autofocus: {},
    disabled: {},
    dropLabel: {
      required: false,
      type: Boolean,
    },
    id: {},
    label: {
      required: true,
      type: String,
    },
    large: {},
    multiple: {},
    name: {},
    options: {},
    required: {},
    showLabel: {
      required: false,
      type: Boolean,
    },
    size: {},
    stacked: {},
    value: {},
  },

  computed: {
    generatedId() {
      return this.id || this.$id();
    },
    ariaLabel() {
      if (this.dropLabel) return this.label;
      return false;
    },
  },

  methods: {
    input(e) {
      this.$emit('input', e);
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "filter"
  ]
}
</lang-strings>
