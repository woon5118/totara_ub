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
.tui-discussionCard {
  display: flex;
  flex-direction: column;
  padding-top: var(--tui-gap-4);
  color: var(--tui-color-neutral-7);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  &__pinBox {
    display: flex;
    justify-content: flex-start;
    margin-bottom: var(--tui-gap-2);
  }

  &:hover,
  &:focus {
    text-decoration: none;
  }

  &__card {
    display: flex;
    padding: var(--tui-gap-4);
    padding-top: 0;

    &__avatar {
      margin-right: var(--tui-gap-2);
    }

    &__content {
      flex-grow: 1;
      width: 100%;

      &__head {
        display: flex;
        flex: 1;
        align-items: flex-end;
        margin-bottom: var(--tui-gap-4);
        padding-top: 2px;

        a {
          @include tui-font-body();
          color: var(--tui-color-state);
          font-weight: bold;
        }

        p {
          @include tui-font-body-x-small();
          margin: 0;
          margin-left: var(--tui-gap-2);
          color: var(--tui-color-neutral-7);
        }
      }

      &__body {
        &--deleted {
          @include tui-font-body-small();
          font-style: italic;
        }
      }

      &__buttons {
        display: flex;

        &__comment {
          display: flex;
          margin-left: var(--tui-gap-4);
        }
      }
    }
  }
}
</style>
