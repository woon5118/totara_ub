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
  @module mod_perform
-->

<style lang="scss">
.tui-performUserActivities {
  &__header-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
  }

  &__heading {
    padding-right: var(--tui-gap-8);
  }

  &__action-button {
    padding-top: var(--tui-gap-2);
  }

  &__content {
    padding-top: var(--tui-gap-4);
  }

  @include tui-font-body();
}

@media (min-width: $tui-screen-xs) {
  .tui-performUserActivities {
    &__content {
      padding-top: 0;
    }

    &__action-button {
      padding-top: 0;
    }
  }
}
</style>
