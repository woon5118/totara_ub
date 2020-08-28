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
.tui-dataTableExpandCell {
  display: flex;
  order: 1;
  margin: var(--tui-gap-2) 0;

  .tui-iconBtn {
    margin: 0 auto;
  }

  &.tui-dataTableExpandCell--header {
    margin-left: 0;
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableExpandCell {
    order: 0;
    width: var(--tui-gap-9);
    margin: 0;

    .tui-iconBtn__text {
      display: none;
    }
  }
}
</style>
