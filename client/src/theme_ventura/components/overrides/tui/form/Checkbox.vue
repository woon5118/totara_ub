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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
:root {
  // Size of checkbox
  --tui-form-checkbox-size: var(--tui-form-input-font-size);
  --tui-form-checkbox-size-large: calc(var(--tui-form-input-font-size) * 1.333);
  --tui-checkbox-check-width: 0.2rem;
}

.tui-checkbox {
  position: relative;
  display: flex;
  height: calc(var(--tui-form-checkbox-size) + 2px);

  &--large {
    height: calc(var(--tui-form-checkbox-size-large) + 2px);
  }

  &__input {
    position: absolute;
    opacity: 0;
  }

  &__label {
    position: relative;
    margin: 0;
    padding-left: calc(var(--tui-form-checkbox-size) * 1.5);
    font-weight: normal;
    font-size: var(--tui-form-input-font-size);
    line-height: 1;

    .tui-checkbox--large & {
      padding-left: calc(var(--tui-form-checkbox-size-large) * 1.5);
    }

    &--noOffset {
      padding-left: var(--tui-form-checkbox-size);
      .tui-checkbox--large & {
        padding-left: var(--tui-form-checkbox-size-large);
      }
    }

    &::before {
      position: absolute;
      top: 0;
      left: 0;
      display: block;
      width: var(--tui-form-checkbox-size);
      height: var(--tui-form-checkbox-size);
      margin-top: 1px;
      background: var(--tui-form-checkbox-bg-color);
      border: var(--tui-form-input-border-size) solid;
      border-color: var(--tui-form-checkbox-border-color);
      transition: border var(--tui-transition-form-function)
          var(--tui-transition-form-duration),
        box-shadow var(--tui-transition-form-function)
          var(--tui-transition-form-duration);
      content: '';
      pointer-events: none;

      .tui-checkbox--large & {
        width: var(--tui-form-checkbox-size-large);
        height: var(--tui-form-checkbox-size-large);
      }
    }
  }

  &__input:disabled ~ &__label {
    color: var(--tui-form-input-text-color-disabled);
  }

  &__input:checked:hover ~ &__label::before,
  &__input:hover ~ &__label::before {
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-checkbox-border-color-focus);
    box-shadow: var(--tui-shadow-1);
  }

  &__input:focus ~ &__label::before {
    background: var(--tui-form-checkbox-bg-color-focus);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-checkbox-border-color-focus);
    box-shadow: var(--tui-shadow-1);
  }

  &__input:disabled:checked ~ &__label::before,
  &__input:disabled ~ &__label::before {
    background: var(--tui-form-checkbox-bg-color-disabled);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-checkbox-border-color-disabled);
    box-shadow: none;
  }

  &__input ~ &__label::after {
    // construct a check mark out of two sides of a rotated box
    position: absolute;
    top: calc(0.267 * var(--tui-form-checkbox-size));
    left: calc(0.133 * var(--tui-form-checkbox-size));
    display: block;
    width: calc(0.733 * var(--tui-form-checkbox-size));
    height: calc(0.4 * var(--tui-form-checkbox-size));
    border-color: var(--tui-form-checkbox-check-color);
    border-style: solid;
    /*rtl:ignore*/
    border-width: 0 0 var(--tui-checkbox-check-width)
      var(--tui-checkbox-check-width);
    transform: rotate(-45deg);
    opacity: 0;
    transition: opacity var(--tui-transition-form-function)
      var(--tui-transition-form-duration);
    content: '';
    pointer-events: none;

    .tui-checkbox--large & {
      top: calc(0.267 * var(--tui-form-checkbox-size-large));
      left: calc(0.133 * var(--tui-form-checkbox-size-large));
      width: calc(0.733 * var(--tui-form-checkbox-size-large));
      height: calc(0.4 * var(--tui-form-checkbox-size-large));
    }
  }

  &__input:disabled:checked ~ &__label::after,
  &__input:disabled ~ &__label::after {
    border-color: var(--tui-form-checkbox-check-color-disabled);
  }

  &__input:checked ~ &__label::before {
    background: var(--tui-form-checkbox-bg-color-active);
  }

  &__input:checked ~ &__label::after {
    opacity: 1;
  }
}
</style>
