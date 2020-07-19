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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <div
    class="tui-progressTrackerCircle tui-progressTrackerCircle__outer"
    :class="[
      state ? 'tui-progressTrackerCircle--' + state : '',
      target ? 'tui-progressTrackerCircle--target' : '',
    ]"
    aria-hidden="true"
  >
    <div class="tui-progressTrackerCircle__middle">
      <div class="tui-progressTrackerCircle__inner" />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    state: {
      default: 'pending',
      type: String,
    },
    target: Boolean,
  },
};
</script>
