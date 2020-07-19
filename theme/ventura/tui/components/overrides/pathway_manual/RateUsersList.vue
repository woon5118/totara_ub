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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-bulkManualRatingRateUsersList {
  display: block;

  &__avatar {
    margin-right: var(--tui-gap-1);
  }

  &__flexRow {
    display: flex;
    flex-direction: row;
  }

  &__search {
    @media (min-width: $tui-screen-sm) {
      width: 50%;
    }
    margin-top: var(--tui-gap-4);
    margin-bottom: var(--tui-gap-4);
  }

  &__noUsers {
    @include tui-font-hint;
  }
}
</style>
