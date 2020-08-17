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
:root {
  --tui-workspace-header-small-height: 55px;
  --tui-workspace-header-medium-height: 88px;

  // Large screen width and height
  --tui-workspace-header-large-height: 135px;
  --tui-workspace-header-large-width: 135px;
}
.tui-workspacePageHeader {
  display: flex;
  flex-direction: column;
  width: 100%;

  @media (min-width: $tui-screen-sm) {
    flex-direction: row;
    align-items: center;
  }

  &__img {
    width: 100%;
    height: var(--tui-workspace-header-small-height);
    margin-bottom: var(--tui-gap-2);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;

    @media (min-width: $tui-screen-xs) {
      height: var(--tui-workspace-header-medium-height);
      margin-bottom: var(--tui-gap-6);
    }

    @media (min-width: $tui-screen-sm) {
      width: 50%;
      max-width: var(--tui-workspace-header-large-width);
      height: var(--tui-workspace-header-large-height);
      margin-right: var(--tui-gap-4);
      margin-bottom: 0;
      border-radius: var(--tui-border-radius-normal);
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    width: 100%;

    &__head {
      padding: 0;
      padding-left: var(--tui-gap-4);

      @media (min-width: $tui-screen-sm) {
        padding: 0;
      }

      &__title {
        display: flex;
        align-items: center;

        &__text {
          width: 100%;
          margin: 0;
          -ms-word-break: break-all;
          word-break: break-word;
          hyphens: none;

          @include tui-font-heading-small();

          @media (min-width: $tui-screen-lg) {
            @include tui-font-heading-medium();
          }
        }

        &__buttonIcon {
          margin-left: var(--tui-gap-2);
        }
      }

      &__subTitle {
        display: flex;
        align-items: center;
        &__text {
          @include tui-font-body();
          margin-top: var(--tui-gap-2);
          color: var(--tui-color-neutral-6);
        }
      }
    }

    &__menu {
      padding: var(--tui-gap-8) 0;
      background-color: var(--tui-color-neutral-3);
      border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    }
  }
}
</style>
