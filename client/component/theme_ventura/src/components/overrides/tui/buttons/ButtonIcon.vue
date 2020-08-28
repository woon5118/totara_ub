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
  @module theme_ventura
-->

<style lang="scss">
.tui-iconBtn {
  @extend .tui-formBtn;

  display: inline-block;
  min-width: 0;
  padding: 0 var(--tui-gap-2);

  // in order to vertically center content in IE we need this display: flex
  // wrapping div because:
  //   * putting `display: flex;` on .tui-iconBtn does not center vertically
  //     due to an IE 11 flex bug
  //     https://github.com/philipwalton/flexbugs#flexbug-3
  //   * relying on vertical-align for centering instead is not good enough -
  //     it's off by a px or two
  &__wrap {
    display: flex;
    align-items: center;
    justify-content: center;

    > .tui-iconBtn__caret {
      margin: 0 var(--tui-gap-1);
    }
  }

  &__icon {
    position: relative;
    top: 1px;
    display: flex;
    flex-shrink: 0;
    font-size: var(--tui-font-size-16);
  }

  &__label {
    display: inline-flex;
    align-items: center;
  }

  &__text {
    -ms-word-break: break-all;
    word-break: break-word;
    hyphens: none;
  }

  &--alert {
    @extend .tui-formBtn--alert;
  }

  &--prim {
    @extend .tui-formBtn--prim;
  }

  &--small {
    @extend .tui-formBtn--small;
    padding: 0 var(--tui-gap-1);
    font-size: var(--tui-font-size-13);

    .tui-iconBtn__icon {
      padding: 0 2px;
      font-size: var(--tui-font-size-14);
    }
  }

  &--xsmall {
    min-height: 2rem;
    padding: 0 0.1rem;
    font-size: var(--tui-font-size-12);

    .tui-iconBtn__icon {
      padding: 0 1px;
      font-size: var(--tui-font-size-11);
    }
  }

  &--small&--hasText,
  &--xsmall&--hasText {
    .tui-iconBtn__icon {
      padding-right: 0;
    }
  }

  &--circle {
    width: 3.6rem;
    min-height: 3.6rem;
    border-radius: 50%;

    &.tui-iconBtn--small {
      width: 3rem;
      height: 3rem;
      min-height: 0;
    }

    &.tui-iconBtn--xsmall {
      width: 2rem;
      height: 2rem;
      min-height: 0;
    }
  }

  &--transparent {
    @extend .tui-formBtn--transparent;
  }

  &--transparent-noPadding {
    @extend .tui-formBtn--transparent;
    &.tui-iconBtn,
    &.tui-iconBtn--small,
    &.tui-iconBtn--xsmall {
      padding: 0;
    }
  }

  &--stealth {
    @extend .tui-formBtn--stealth;
  }

  &--textFirst &__label {
    flex-direction: row-reverse;
    .tui-iconBtn__text {
      padding: 0 var(--tui-gap-1);
    }
  }

  &--toolbar {
    @extend .tui-formBtn--toolbar;
  }

  &--selected {
    @extend .tui-formBtn--selected;
  }

  &--toolbar&--selected {
    @extend .tui-formBtn--toolbar.tui-formBtn--selected;
  }
}

.tui-iconBtn__text {
  padding: 0 var(--tui-gap-1);
}
</style>
