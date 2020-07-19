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
  <div class="tui-checkboxButton">
    <input
      :id="id"
      class="tui-checkboxButton__input"
      type="checkbox"
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
    <label class="tui-checkboxButton__label" :for="id">
      <slot />
      <CloseIcon custom-class="tui-checkboxButton__deselectIcon" />
    </label>
  </div>
</template>

<script>
import CloseIcon from 'totara_core/components/icons/common/Close';

export default {
  components: {
    CloseIcon,
  },

  model: {
    prop: 'checked',
    event: 'change',
  },

  props: {
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
    name: String,
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
