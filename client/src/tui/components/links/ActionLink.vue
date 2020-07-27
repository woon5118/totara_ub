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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <a
    class="tui-actionLink"
    :aria-label="ariaLabel"
    :href="disabled ? false : href"
    :class="{
      'tui-actionLink--prim': styleclass.primary,
      'tui-actionLink--small': styleclass.small,
      'tui-formBtn--srOnly': styleclass.srOnly,
    }"
    @click="handleClick"
  >
    {{ text }}
  </a>
</template>

<script>
export default {
  props: {
    styleclass: {
      default: () => ({
        primary: false,
        small: false,
        srOnly: false,
      }),
      type: Object,
    },
    ariaLabel: String,
    disabled: Boolean,
    href: {
      required: true,
      type: String,
    },
    text: {
      required: true,
      type: String,
    },
  },

  methods: {
    handleClick(e) {
      if (this.disabled) {
        e.preventDefault();
        e.stopPropagation();
        return;
      }
      this.$emit('click', e);
    },
  },
};
</script>
