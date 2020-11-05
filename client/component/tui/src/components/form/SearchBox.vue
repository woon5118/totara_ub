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
  @module tui
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
      :placeholder="placeholder || $str('search', 'core')"
      :styleclass="{ postIcon: enableClearIcon }"
      class="tui-searchBox__search"
      @input="input"
      @submit="submit"
    />
    <div v-if="isClearIconVisible" class="tui-searchBox__clearContainer">
      <ButtonIcon
        :aria-label="$str('clear_search_term', 'totara_core')"
        :disabled="disabled"
        :styleclass="{ small: true, transparent: true }"
        @click="clear"
      >
        <RemoveIcon class="tui-searchBox__removeIcon" />
      </ButtonIcon>
    </div>
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
import SearchIcon from 'tui/components/icons/Search';
import RemoveIcon from 'tui/components/icons/Remove';

export default {
  components: {
    ButtonIcon,
    InputSearch,
    Label,
    SearchIcon,
    RemoveIcon,
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
    'enableClearIcon',
  ],

  data() {
    return {
      isClearIconVisible: this.enableClearIcon ? !!this.value : false,
    };
  },

  computed: {
    generatedId() {
      return this.id || this.$id();
    },
  },

  methods: {
    input(value) {
      if (this.enableClearIcon) {
        this.isClearIconVisible = !!value;
      }
      this.$emit('input', value);
    },

    clear() {
      this.isClearIconVisible = false;
      this.$emit('clear');
    },

    submit() {
      this.$emit('submit');
    },
  },
};
</script>

<lang-strings>
{
  "core": [
    "search"
  ],
  "totara_core": [
    "clear_search_term"
  ]
}
</lang-strings>

<style lang="scss">
.tui-searchBox {
  position: relative;
  display: flex;

  &__search {
    // disable the default clear (x) action in IE
    &::-ms-clear {
      display: none;
    }
  }

  .tui-formLabel {
    margin-right: var(--gap-2);
  }

  &__clearContainer {
    position: absolute;
    right: var(--gap-8);
    display: flex;
    align-items: center;
    height: 100%;
  }

  &__removeIcon {
    color: var(--color-neutral-6);
  }

  // So that the search button matches the format of the input that is next to it
  &__button {
    border-color: var(--form-input-border-color);
    border-style: solid;
    border-width: var(--form-input-border-size) var(--form-input-border-size)
      var(--form-input-border-size) 0;

    &:hover,
    &:active,
    &:focus {
      background-color: var(--btn-bg-color-focus);
      border-color: var(--form-input-border-color);
      border-style: solid;
      border-width: var(--form-input-border-size) var(--form-input-border-size)
        var(--form-input-border-size) 0;
      box-shadow: var(--btn-shadow-focus);
    }
  }
}
</style>
