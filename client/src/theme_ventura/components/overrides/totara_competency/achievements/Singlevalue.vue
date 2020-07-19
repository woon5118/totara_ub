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
.tui-competencyAchievementsScale {
  & > * + * {
    margin-top: var(--tui-gap-3);
  }

  &__title {
    margin-left: var(--tui-gap-2);
    @include tui-font-heading-small();
  }

  &__item {
    margin-top: var(--tui-gap-4);
    &:first-of-type {
      margin-top: var(--tui-gap-2);
    }
  }

  &__separator {
    margin: var(--tui-gap-4) var(--tui-gap-4) 0;
  }
}
</style>
