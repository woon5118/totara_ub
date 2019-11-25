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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_core
-->

<template>
  <button
    tabindex="-1"
    class="tui-dropdownButton"
    type="button"
    :class="{
      'tui-dropdownButton--disabled': disabled,
    }"
    :disabled="disabled"
    @click="clickDropdownButton"
  >
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    disabled: Boolean,
  },

  methods: {
    clickDropdownButton(e) {
      if (this.disabled) {
        e.stopPropagation();
        return;
      }

      this.$emit('click', e);
    },
  },
};
</script>
