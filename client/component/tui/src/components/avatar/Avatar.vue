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
  <img
    :src="src"
    :alt="alt"
    class="tui-avatar"
    :class="`tui-avatar--${size}`"
  />
</template>

<script>
export default {
  props: {
    src: {
      required: true,
      type: String,
    },

    alt: {
      required: true,
      type: String,
    },

    size: {
      type: String,
      default: 'medium',
      validator(value) {
        return ['large', 'medium', 'small', 'xsmall', 'xxsmall'].includes(
          value
        );
      },
    },
  },
};
</script>
