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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-instanceInfoCard {
  &__title {
    @include tui-font-heading-x-small();
    margin-top: 0;
  }

  &__info-section {
    @include tui-font-body();
    display: table;
  }

  &__info-row {
    display: table-row;
  }

  &__info-label {
    display: table-cell;
    padding-right: var(--tui-gap-8);
    padding-bottom: var(--tui-gap-3);
  }

  &__info-value {
    display: table-cell;
    padding-bottom: var(--tui-gap-3);
  }
}
</style>
