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
  <img class="icon" :src="src" :alt="alt" :title="title" />
</template>

<script>
import { imageUrl } from 'tui/util';

// pix icon implementation
// do not use this directly, use <FlexIcon> for pix icons too.
export default {
  props: {
    icon: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: undefined,
    },
    alt: {
      type: String,
      default: undefined,
    },
  },

  computed: {
    src() {
      const parts = this.icon.split('|');

      if (parts.length === 1) {
        return imageUrl(parts[0], 'core');
      }

      return imageUrl(parts[1], parts[0]);
    },
  },
};
</script>
