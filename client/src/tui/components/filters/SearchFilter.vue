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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <div
    class="tui-searchFilter"
    :class="{ 'tui-searchFilter--stacked': stacked }"
  >
    <Label
      v-if="!dropLabel"
      :for-id="generatedId"
      :hidden="!showLabel"
      :label="label"
    />
    <div class="tui-searchFilter__group">
      <div class="tui-searchFilter__group-icon">
        <SearchIcon />
      </div>
      <InputSearch
        :id="generatedId"
        v-bind="$props"
        :aria-label="ariaLabel || label"
        :styleclass="{ preIcon: true }"
        @input="input"
        @submit="submit"
      />
    </div>
  </div>
</template>

<script>
// Components
import InputSearch from 'tui/components/form/InputSearch';
import Label from 'tui/components/form/Label';
import SearchIcon from 'tui/components/icons/common/Search';

export default {
  components: {
    InputSearch,
    Label,
    SearchIcon,
  },
  inheritAttrs: false,

  /* eslint-disable vue/require-prop-types */
  props: {
    ariaDescribedby: {},
    ariaLabel: {},
    ariaLabelledby: {},
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
    list: {},
    maxlength: {},
    minlength: {},
    name: {},
    pattern: {},
    placeholder: {},
    showLabel: {
      required: false,
      type: Boolean,
    },
    size: {},
    spellcheck: {},
    stacked: {},
    value: {},
  },

  computed: {
    generatedId() {
      return this.id || this.$id();
    },
  },

  methods: {
    input(e) {
      this.$emit('input', e);
    },

    submit() {
      this.$emit('submit');
    },
  },
};
</script>
