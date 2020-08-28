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
  @module tui
-->

<template>
  <div
    v-if="wrap"
    v-focus-within
    class="tui-dataTableRowGroup"
    :class="{
      'tui-dataTableRowGroup--selected': selected,
    }"
    role="rowgroup"
  >
    <slot />
  </div>
  <div v-else class="tui-dataTableRows">
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    selected: Boolean,
    wrap: Boolean,
  },
};
</script>
