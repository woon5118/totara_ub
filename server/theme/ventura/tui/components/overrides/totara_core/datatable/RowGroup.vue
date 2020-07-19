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
  @module theme_ventura
-->

<style lang="scss">
.tui-dataTableRowGroup {
  padding: 0 var(--tui-gap-2);
  border-top: 2px solid var(--tui-datatable-row-first-border-color);

  & + & {
    border-top: 1px solid var(--tui-datatable-row-border-color);
  }

  &:last-child {
    border-bottom: 1px solid var(--tui-datatable-row-border-color);
  }

  &.tui-focusWithin,
  &:active,
  &:hover {
    background: var(--tui-datatable-row-bg-color-focus);
  }

  &--selected {
    background: var(--tui-datatable-row-bg-color-active);

    &:hover {
      background: var(--tui-datatable-row-bg-color-focus);
    }
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableRowGroup {
    padding: 0;
  }
}
</style>
