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
.tui-competencySummaryAchievementConfiguration {
  & > * + * {
    margin-top: var(--tui-gap-2);
  }

  &__aggregation {
    margin-top: var(--tui-gap-4);
    &-label {
      @include tui-font-heavy();
      margin-right: var(--tui-gap-2);
    }
  }

  &__noPaths {
    padding: var(--tui-font-size-8);
    font-style: italic;
  }

  &__pathGroup {
    flex-grow: 1;
    padding: var(--tui-gap-4);

    & > * + * {
      &.tui-grid {
        margin-top: var(--tui-gap-4);
        padding-top: var(--tui-gap-4);
        border-top: var(--tui-border-width-thin) solid
          var(--tui-card-border-color);
      }
    }
  }

  &__scaleValue {
    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-or {
      width: 50%;
      margin: 0;
    }
  }
}
</style>
