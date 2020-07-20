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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-bulkManualRatingCell {
  &__newRating {
    @media (min-width: $tui-screen-sm) {
      display: flex;
      flex-direction: row-reverse;
    }
  }

  &__rating {
    display: flex;
    align-items: center;
    margin-right: var(--tui-gap-2);

    &-hasComment {
      margin-left: var(--tui-gap-1);
    }

    &-noValue {
      @include tui-font-hint;
    }

    &-valueName {
      @include tui-font-heading-label;
    }
  }
}
</style>
