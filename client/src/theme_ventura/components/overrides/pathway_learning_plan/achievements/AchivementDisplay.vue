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
.tui-pathwayLearningPlanAchievement {
  &__empty {
    font-size: var(--tui-font-size-16);
    @include tui-font-hint();
  }

  &__comment {
    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-body {
      margin-top: var(--tui-gap-2);
    }
  }

  &__overview {
    max-width: 100%;
  }

  &__summary {
    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-body {
      margin-top: var(--tui-gap-4);
    }

    &-button {
      margin-top: var(--tui-gap-4);
    }
  }

  &__title {
    margin: 0;
    @include tui-font-heading-x-small();
    text-align: center;
  }

  &__noValue {
    margin-top: var(--tui-gap-4);
    @include tui-font-hint();
  }

  &__rater {
    display: flex;

    &-name {
      margin: auto 0 auto var(--tui-gap-2);
    }
  }

  &__rating {
    display: flex;

    &-icon {
      position: relative;
      top: -1px;
    }

    &-value {
      margin-right: var(--tui-gap-1);
    }
  }

  &__value {
    margin-top: var(--tui-gap-4);

    &-title {
      @include tui-font-heavy();
    }
  }

  &__content {
    padding: var(--tui-gap-4);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: 6px;
  }
}
</style>
