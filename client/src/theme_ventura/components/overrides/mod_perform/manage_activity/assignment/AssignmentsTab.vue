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
.tui-performManageActivityAssignmentsForm {
  &__heading {
    align-items: center;
    margin-top: var(--tui-gap-2); // Check the content tab before changing this.

    &-title {
      @include tui-font-heading-small();
    }

    &-buttons {
      display: flex;
    }

    &-dropdown {
      margin-top: var(--tui-gap-2);
    }
  }

  &__table {
    margin-top: var(--tui-gap-4);
  }

  &__summary {
    display: flex;
    margin-top: var(--tui-gap-4);

    &-count {
      display: flex;
      flex-direction: column;
      justify-content: center;
      margin-right: var(--tui-gap-4);
    }
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-performManageActivityAssignmentsForm {
    &__heading {
      &-dropdown {
        margin-top: 0;
        margin-left: auto;
      }
    }
  }
}
</style>
