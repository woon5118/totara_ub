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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module tui
-->

<template>
  <div
    v-if="this.$slots['default']"
    class="tui-separator tui-separator--wrapped"
    :class="[
      thick ? 'tui-separator--thick' : '',
      spread ? 'tui-separator--spread' : '',
    ]"
  >
    <span class="tui-separator-content">
      <slot />
    </span>
    <hr class="sr-only" />
  </div>
  <hr
    v-else
    class="tui-separator tui-separator-rule"
    :class="[
      thick ? 'tui-separator--thick' : '',
      spread ? 'tui-separator--spread' : '',
    ]"
  />
</template>
<script>
export default {
  props: {
    /**
     * When provided, a modifier className is added to the element that
     * references a SCSS variable to set a thicker visible horizontal rule
     **/
    thick: {
      type: Boolean,
      default: function() {
        return false;
      },
    },

    /**
     * When provided, a modifier className is added to the element that
     * references a SCSS variable to set increased vertical margins
     **/
    spread: {
      type: Boolean,
      default: function() {
        return false;
      },
    },
  },
};
</script>
