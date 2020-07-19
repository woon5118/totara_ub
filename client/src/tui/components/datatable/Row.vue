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
    v-focus-within="!inGroup"
    class="tui-dataTableRow"
    :class="{
      'tui-dataTableRow--colorOdd': colorOdd,
      'tui-dataTableRow--disabled': disabled,
      'tui-dataTableRow--inGroup': inGroup,
      'tui-dataTableRow--selected': selected,
      'tui-dataTableRow--draggable': draggable,
      'tui-dataTableRow--dragging': dragging,
      'tui-dataTableRow--borderTopFirstOff': borderTopHidden,
      'tui-dataTableRow--borderBottomLastOff': borderBottomHidden,
      'tui-dataTableRow--borderSeparatorOff': borderSeparatorHidden,
      'tui-dataTableRow--hoverOff': hoverOff,
    }"
    role="row"
  >
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    borderBottomHidden: Boolean,
    borderSeparatorHidden: Boolean,
    borderTopHidden: Boolean,
    colorOdd: Boolean,
    disabled: Boolean,
    hoverOff: Boolean,
    inGroup: Boolean,
    selected: Boolean,
    draggable: Boolean,
    dragging: Boolean,
  },
};
</script>
