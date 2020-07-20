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
  @module theme_ventura
-->

<style lang="scss">
.tui-dataTable {
  &--archived {
    background: var(--tui-datatable-bg-archived);
  }

  &__row-move-cell {
    flex-basis: var(--tui-gap-5);
    flex-grow: 0;
  }

  &__row-move-menu {
    position: absolute;
    top: 0;
    left: var(--tui-gap-8);
    background: var(--tui-color-background);
  }
}
</style>
