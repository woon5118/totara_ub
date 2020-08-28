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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-playlistRelatedCard {
  display: flex;
  min-width: 120px;
  height: 82px;
  background-color: var(--tui-color-neutral-1);

  &__header {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    margin-right: var(--tui-gap-2);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    border-top-left-radius: var(--tui-border-radius-normal);
    border-bottom-left-radius: var(--tui-border-radius-normal);

    > :first-child {
      @include tui-font-heading-label-small();
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      background-color: var(--tui-color-neutral-1);
      border-radius: 50%;
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: var(--tui-gap-4) 0 var(--tui-gap-2) 0;
    overflow: hidden;

    & > * {
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    > :first-child {
      @include tui-font-heading-label-small();
      color: inherit;
      text-decoration: none;
    }

    > :nth-child(2) {
      @include tui-font-body-x-small();
    }

    > :last-child {
      margin-top: auto;
      margin-bottom: 0;

      .tui-totaraEngage-star {
        width: var(--tui-font-size-14);
        height: var(--tui-font-size-14);

        &__filled {
          stop-color: var(--tui-color-chart-background-2);
        }

        &__unfilled {
          stop-color: var(--tui-color-neutral-1);
        }
      }
    }
  }

  &__bookmark {
    align-self: flex-start;
    // neutralize the default icon padding
    margin-top: -2px;
  }
}
</style>
