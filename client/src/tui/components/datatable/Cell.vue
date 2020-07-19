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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module tui
-->

<template>
  <div
    class="tui-dataTableCell"
    :class="[
      align && 'tui-dataTableCell--align-' + align,
      heavy && 'tui-dataTableCell--heavy',
      repeatedHeader && 'tui-dataTableCell--repeatedHeader',
      size && 'tui-dataTableCell--size_' + size,
      valign && 'tui-dataTableCell--valign-' + valign,
    ]"
    role="cell"
  >
    <span v-if="columnHeader" class="tui-dataTableCell__label" aria-hidden>
      {{ columnHeader }}
    </span>
    <div class="tui-dataTableCell__content">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    align: {
      type: String,
      validator: val => ['start', 'center', 'end'].indexOf(val) !== -1,
    },
    columnHeader: String,
    heavy: Boolean,
    repeatedHeader: Boolean,
    size: String,
    valign: {
      type: String,
      validator: val => ['start', 'center', 'end'].indexOf(val) !== -1,
    },
  },
};
</script>
