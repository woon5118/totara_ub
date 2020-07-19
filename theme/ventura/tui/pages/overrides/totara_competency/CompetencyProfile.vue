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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-competencyProfile {
  // disable scroll anchoring as it is problematic when switching between chart/list views
  overflow-anchor: none;

  &__split {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
  }

  &__titleSection {
    margin: var(--tui-gap-6) 0 var(--tui-gap-4) 0;
  }

  &__title {
    @include tui-font-heading-medium();
    margin: 0;
  }

  &__buttons {
    > * {
      margin-top: var(--tui-gap-4);
    }
  }

  &__sectionTitle {
    @include tui-font-heading-small();
    margin: var(--tui-gap-4) 0 var(--tui-gap-2) 0;
  }

  &__filtersBar {
    margin: var(--tui-gap-6) 0;
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-competencyProfile {
    &__buttons {
      > * {
        margin-top: 0;
      }
    }
  }
}
</style>
