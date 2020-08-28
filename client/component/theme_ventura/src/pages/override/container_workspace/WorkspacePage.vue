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
.tui-workspacePage {
  .tui-layoutTwoColumn {
    &__heading {
      display: none;
    }
  }

  &__mainContent {
    margin: 0;

    @media (min-width: $tui-screen-sm) {
      margin-top: var(--tui-gap-8);
    }

    &__primaryAction {
      display: flex;
      width: 100%;
      margin-top: var(--tui-gap-4);

      @media (min-width: $tui-screen-sm) {
        justify-content: flex-end;
      }

      &__action {
        width: 100%;

        @media (min-width: $tui-screen-sm) {
          width: inherit;
        }
      }
    }

    &__tabs {
      margin-top: var(--tui-gap-4);
      .tui-tabs__tabs {
        padding-left: var(--tui-gap-4);
        @media (min-width: $tui-screen-sm) {
          padding: 0;
        }
      }

      &__text {
        @include tui-font-body();
      }

      &__discussionTab {
        margin-top: var(--tui-gap-4);
      }
    }
  }
}
</style>
