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
  @module tui
-->

<template>
  <a
    v-if="href"
    :href="href"
    class="tui-tag tui-tag__content"
    role="tag-link"
    >{{ text }}</a
  >

  <div v-else class="tui-tag" :class="{ 'tui-tag--primary': primary }">
    <span
      class="tui-tag__content"
      :class="{ 'tui-tag__content--withButton': $slots.button }"
      >{{ text }}</span
    >
    <slot name="button" />
  </div>
</template>

<script>
export default {
  props: {
    primary: Boolean,
    text: {
      type: String,
      required: true,
    },
    href: String,
  },
};
</script>
