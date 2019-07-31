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
.tui-accessDisplay {
  &__accessIcon {
    display: flex;
    // neutralize the defalut flexicon padding
    margin-left: -7px;
    &__icons {
      display: flex;
      align-items: center;
    }
    &__shareButton {
      margin-left: var(--tui-gap-4);

      @media (max-width: $tui-screen-md) {
        margin-left: var(--tui-gap-2);
      }
    }
  }

  &__timeView {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin-top: var(--tui-gap-2);
    margin-left: -3px;
    > :first-child {
      margin-left: calc(var(--tui-gap-1) * -1);
    }
  }

  &__topics {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin-top: var(--tui-gap-1);
    margin-left: -2px;
    line-height: normal;

    &__topic {
      @include tui-font-body-small();
      margin-top: var(--tui-gap-1);
      margin-right: var(--tui-gap-1);
    }

    > :first-child {
      margin-right: var(--tui-gap-1);
      color: var(--tui-color-neutral-6);
    }
  }

  &__editSettings {
    margin-top: var(--tui-gap-2);

    &__text {
      @include tui-font-body-small();
      color: var(--tui-color-state);

      &:focus,
      &:hover {
        text-decoration: underline;
        cursor: pointer;
      }
    }
  }
}
</style>
