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
.tui-actionCard {
  flex-direction: column;
  padding: var(--tui-gap-4);

  &__body {
    display: flex;
    flex-grow: 1;
    flex-shrink: 1;
    flex-wrap: wrap;
    align-items: center;
  }

  &__action {
    display: flex;
    flex-shrink: 0;
    align-items: normal;
    padding: var(--tui-gap-4) 0 0;
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-actionCard {
    flex-direction: row;
    &--stacked {
      flex-direction: column;
    }

    &:not(&--stacked) &__action {
      align-items: center;
      padding: 0 0 0 var(--tui-gap-4);
    }
  }
}
</style>
