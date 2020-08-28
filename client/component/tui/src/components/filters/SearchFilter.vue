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
import SearchIcon from 'tui/components/icons/Search';

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

<style lang="scss">
.tui-searchFilter {
  position: relative;
  display: flex;
  flex-direction: row;
  align-items: center;

  input.tui-formInput {
    flex-grow: 0;
  }

  .tui-formLabel {
    margin: auto var(--gap-3) auto 0;
  }

  &__group {
    position: relative;
    display: flex;
    flex-grow: 1;
    margin-top: 0;
    margin-bottom: auto;

    &--stacked {
      margin-top: var(--gap-1);
    }

    &-icon {
      position: absolute;
      left: var(--gap-1);
      display: flex;
      align-items: center;
      height: 100%;
      color: var(--filter-search-icon-color);

      .fa-search {
        margin: auto 0;
      }
    }
  }

  &--stacked {
    flex-direction: column;
    align-items: stretch;

    .tui-formLabel {
      margin: var(--gap-1) 0;
    }
  }
}
</style>
