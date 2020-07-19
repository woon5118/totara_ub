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
.tui-competencySummaryAchievementCriteria {
  width: 100%;

  &--multi {
    padding: var(--tui-gap-4);
  }

  &__and {
    width: 50%;
    margin: 0;
  }

  &__criterion {
    &-header {
      display: inline-block;
      width: 40%;
      margin: 0;
      @include tui-font-heading-label();
    }

    &-aggregation {
      @include tui-font-hint();
    }

    &-items {
      margin-top: var(--tui-gap-1);
    }
  }
}
</style>
