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
    margin: 0;
    @include tui-font-heading-x-small;
  }

  &__list {
    margin: var(--tui-gap-4) 0 0;

    & > * + * {
      margin-top: var(--tui-gap-2);
    }

    &-item {
      display: flex;
    }
  }

  &__proficientStatus {
    min-width: var(--tui-gap-8);

    .flex-icon {
      position: relative;
      top: -1px;
    }
  }

  &__scaleValue {
    display: flex;
    flex-direction: column;

    &-name {
      @include tui-font-heading-label;
      margin: 0;
    }

    &-description {
      margin: var(--tui-gap-1) 0 0 var(--tui-gap-3);
    }
  }
}
</style>
