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
.tui-collapsible {
  &__header {
    display: flex;
    padding: var(--tui-gap-2) var(--tui-gap-2) var(--tui-gap-2) 0;
    background: var(--tui-collapsible-header-bg-color);
    border: var(--tui-border-width-thin) solid
      var(--tui-collapsible-header-border-color);

    &_icons {
      padding-right: var(--tui-gap-4);
      padding-left: var(--tui-gap-4);
    }

    &-text {
      @include tui-font-heading-x-small();
      margin: 0;
    }

    &-sideContent {
      display: flex;
      flex-shrink: 0;
      margin: auto 0 auto auto;
    }
  }
}
</style>
