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
  <div class="tui-searchBox">
    <Label
      v-if="!dropLabel"
      :for-id="generatedId"
      :hidden="!labelVisible"
      :label="ariaLabel || placeholder"
    />
    <InputSearch
      :id="generatedId"
      v-bind="$props"
      :aria-label="ariaLabel || placeholder"
      :disabled="disabled"
      :placeholder="placeholder || $str('search', 'moodle')"
      @input="input"
      @submit="submit"
    />
    <ButtonIcon
      :aria-label="ariaLabel || placeholder"
      :disabled="disabled"
      :styleclass="{ small: true, transparent: true }"
      class="tui-searchBox__button"
      @click="submit"
    >
      <SearchIcon />
    </ButtonIcon>
  </div>
</template>

<script>
// Components
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import InputSearch from 'tui/components/form/InputSearch';
import Label from 'tui/components/form/Label';
import SearchIcon from 'tui/components/icons/common/Search';

export default {
  components: {
    ButtonIcon,
    InputSearch,
    Label,
    SearchIcon,
  },
  inheritAttrs: false,

  /* eslint-disable vue/require-prop-types */
  props: [
    'ariaDescribedby',
    'ariaLabel',
    'ariaLabelledby',
    'disabled',
    'dropLabel',
    'id',
    'labelVisible',
    'list',
    'maxlength',
    'minlength',
    'name',
    'pattern',
    'placeholder',
    'size',
    'spellcheck',
    'value',
  ],

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

<lang-strings>
{
  "moodle": [
    "search"
  ]
}
</lang-strings>
