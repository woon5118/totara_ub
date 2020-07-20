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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-competencyRatingScaleOverview {
  &__title {
    @include tui-font-heading-x-small;
    margin-top: 0;
    margin-bottom: var(--tui-gap-2);
  }

  &__list {
    margin: 0;

    &-item {
      display: flex;

      &:not(:last-child) {
        padding-bottom: var(--tui-gap-2);
      }
    }
  }

  &__proficientStatus {
    min-width: var(--tui-gap-8);
  }

  &__scaleValue {
    display: flex;
    flex-direction: column;

    &-name {
      @include tui-font-heading-label;
      margin: 0;
    }

    &-description {
      padding-top: var(--tui-gap-1);
      padding-left: var(--tui-gap-3);
    }
  }
}
</style>
