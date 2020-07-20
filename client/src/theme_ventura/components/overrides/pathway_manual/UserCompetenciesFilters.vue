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
.tui-bulkManualUserCompetenciesFilters {
  &__singleCompetencyMessage {
    &-text {
      margin-right: var(--tui-gap-2);
      @include tui-font-hint;
    }

    @media (max-width: $tui-screen-sm) {
      display: flex;
      align-content: space-between;

      &-text {
        margin-right: var(--tui-gap-4);
      }

      &-button {
        flex-shrink: 0;
      }
    }
  }
}
</style>
