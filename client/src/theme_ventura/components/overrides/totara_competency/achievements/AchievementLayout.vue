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
.tui-achievementLayout {
  &__left {
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    padding-right: var(--tui-gap-4);
  }

  &--borderless &__left {
    border-right: none;
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-achievementLayout {
    &__left {
      border-right: var(--tui-border-width-normal) solid
        var(--tui-color-neutral-5);

      .tui-grid--stacked & {
        border-right: none;
      }
    }
  }
}
</style>
