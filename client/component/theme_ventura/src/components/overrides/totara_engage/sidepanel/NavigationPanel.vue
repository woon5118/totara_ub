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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-navigationPanel {
  &__selected {
    margin-top: var(--tui-gap-6);
    margin-bottom: var(--tui-gap-2);
  }

  &__menu {
    height: 100%;
    overflow: auto;
  }

  &__search {
    margin: var(--tui-gap-4);
    margin-right: var(--tui-gap-1);
    margin-bottom: var(--tui-gap-6);
  }

  &__category {
    @include tui-font-heading-label;
    display: flex;
    align-items: center;
    margin: var(--tui-gap-8) var(--tui-gap-4);
    margin-bottom: var(--tui-gap-3);

    & > span {
      padding-right: var(--tui-gap-2);
    }
  }

  &__header {
    margin-top: 0;
    margin-bottom: 0;
    @include tui-font-heading-label();
    & .tui-navigationPanel__btn {
      @include tui-font-heading-small;
      color: var(--tui-color-text);
      .tui-caret {
        color: var(--tui-color-state);
      }
    }
  }

  &__contribute {
    align-items: center;
    justify-content: space-between;
  }

  &__link {
    display: flex;
    align-items: center;
    padding: var(--tui-gap-1) var(--tui-gap-4);

    a {
      text-decoration: none;
    }

    &--inactive {
      .tui-navigationPanel__link__text {
        @include tui-font-link;
        color: var(--tui-color-state-focus);
      }

      &:hover,
      &:focus {
        background-color: var(--tui-color-state-highlight);
      }
    }

    &--active {
      background-color: var(--tui-color-state-active);

      .tui-navigationPanel__link__text {
        @include tui-font-link;
        color: var(--tui-color-neutral-1);

        &:hover,
        &:focus {
          color: var(--tui-color-neutral-1);
        }
      }
    }
  }

  /* Vertical grid styles */
  &__vertical {
    border-bottom: 1px solid var(--tui-color-neutral-5);

    .tui-navigationPanel {
      &__selected {
        padding: 0 var(--tui-gap-4);
      }

      &__search {
        margin-bottom: var(--tui-gap-8);
      }

      &__link {
        padding: var(--tui-gap-2) var(--tui-gap-4);
        border-bottom: 1px solid var(--tui-color-neutral-4);

        &__first {
          border-top: 1px solid var(--tui-color-neutral-4);
        }
      }

      &__menu {
        z-index: var(--tui-zindex-dropdown-menu);
        width: 100%;
        margin-bottom: var(--tui-gap-4);
        background-color: var(--tui-color-neutral-3);
        border: 1px solid var(--tui-color-neutral-5);
      }
    }
  }
}
</style>
