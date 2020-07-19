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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_core
-->

<template>
  <a
    :href="href || '#'"
    tabindex="-1"
    class="tui-dropdownItem"
    :class="{
      'tui-dropdownItem--disabled': disabled,
      'tui-dropdownItem--paddingless': paddingless,
      'tui-dropdownItem--small': small,
    }"
    :aria-disabled="disabled"
    :role="role"
    @click="clickItem"
  >
    <slot />
  </a>
</template>

<script>
export default {
  props: {
    role: {
      type: String,
      default: 'menuitem',
    },
    disabled: Boolean,
    href: String,
    paddingless: Boolean,
    isDropdown: Boolean,
    small: Boolean,
  },

  methods: {
    clickItem(e) {
      if (!this.href) {
        e.preventDefault();
        this.$emit('click');
        return;
      }

      if (this.disabled) {
        e.stopPropagation();
        e.preventDefault();
        return;
      }

      this.$emit('click');
    },
  },
};
</script>
