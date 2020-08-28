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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module totara_engage
-->

<template>
  <div class="tui-totaraEngage-imageHeader">
    <div v-if="showCover" class="tui-totaraEngage-imageHeader__actionContainer">
      <!-- This is where the icon should be -->
      <slot name="actions" />
    </div>

    <slot name="image" />
  </div>
</template>

<script>
export default {
  props: {
    showCover: {
      required: true,
      type: Boolean,
    },
  },
};
</script>
