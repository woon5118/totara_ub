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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-workspaceMembersTab {
  padding-top: var(--tui-gap-4);

  &__list {
    width: 100%;
    margin-bottom: var(--tui-gap-8);
  }

  &__head {
    display: flex;
    justify-content: flex-start;
    margin-bottom: var(--tui-gap-8);
    padding-bottom: var(--tui-gap-4);
    padding-left: var(--tui-gap-4);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

    @media (min-width: $tui-screen-sm) {
      justify-content: flex-end;
      padding-left: 0;
    }
  }

  &__content {
    padding-left: var(--tui-gap-4);

    @media (min-width: $tui-screen-sm) {
      padding-left: 0;
    }

    &__head {
      display: flex;
      justify-content: space-between;
      margin-bottom: var(--tui-gap-4);

      &__title {
        @include tui-font-heading-x-small();
        margin: 0;
      }
    }

    &__members {
      &__member {
        margin-bottom: var(--tui-gap-2);
      }

      &__message {
        @include tui-font-body();
      }

      &__loadMoreContainer {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-top: var(--tui-gap-6);
        padding-bottom: var(--tui-gap-8);
      }

      &__viewedMembers {
        display: flex;
        align-self: center;
        margin-bottom: var(--tui-gap-1);
      }

      &__loadMore {
        display: flex;
        align-self: center;
      }
    }
  }
}
</style>
