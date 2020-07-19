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
.tui-pathwayManualAchievementRater {
  margin-top: var(--tui-gap-2);
  padding: var(--tui-gap-4);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-radius: 6px;

  &__overview {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    align-items: center;

    &-role {
      @include tui-font-heavy();
    }
  }
}
</style>
