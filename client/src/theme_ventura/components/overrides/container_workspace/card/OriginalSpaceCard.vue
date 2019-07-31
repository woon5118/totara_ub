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
.tui-originalSpaceCard {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-radius: var(--tui-border-radius-normal);
  transition: box-shadow var(--tui-transition-form-function)
    var(--tui-transition-form-duration);

  &:hover {
    cursor: pointer;
  }

  &:hover,
  &:focus {
    box-shadow: var(--tui-shadow-2);
  }

  &:focus-within,
  &.tui-focusWithin {
    box-shadow: var(--tui-shadow-2);
  }

  &__titleBox {
    display: flex;
    flex-basis: 50%;
    flex-direction: column;
    width: 100%;
    padding: var(--tui-gap-4) var(--tui-gap-2);
    padding-bottom: 0;
    background-image: linear-gradient(
      to top,
      transparent 0%,
      var(--tui-color-backdrop-heavy) 78%,
      var(--tui-color-backdrop-heavy)
    );

    border-top-left-radius: var(--tui-border-radius-normal);
    border-top-right-radius: var(--tui-border-radius-normal);

    &__title {
      @include tui-font-heading-x-small();
      margin: 0;
    }
  }

  &__link {
    color: var(--tui-color-neutral-1);
    &:hover,
    &:focus {
      color: var(--tui-color-neutral-1);
      text-decoration: none;
      outline: none;
    }
  }

  &__actionBox {
    display: flex;
    flex-basis: 50%;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    width: 100%;

    &__button,
    &__dropDown {
      margin-bottom: var(--tui-gap-4);
    }
  }
}
</style>
