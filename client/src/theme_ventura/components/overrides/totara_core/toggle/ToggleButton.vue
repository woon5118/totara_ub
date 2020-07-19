<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
:root {
  --tui-form-toggle-color: var(--tui-color-neutral-7);
  --tui-form-toggle-dot-size: 1.6rem;
  --tui-form-toggle-container-width: 4rem;
  --tui-form-toggle-container-height: 2rem;
  --tui-form-toggle-container-radius: 1rem;
  --tui-form-toggle-text-offset: var(--tui-gap-2);
  --tui-form-toggle-dot-offset: 0.2rem;
  --tui-form-toggle-bottom: 1.8rem;
}

.tui-toggleSwitch {
  display: flex;
  align-items: center;

  &__btn {
    @extend .tui-formBtn;
    @extend .tui-formBtn--transparent;
    color: var(--tui-form-toggle-color);

    &:focus,
    &:active:focus {
      color: var(--tui-form-toggle-color);
      outline: none;
    }

    &:hover {
      color: var(--tui-form-toggle-color);
    }

    &[disabled] {
      opacity: 0.4;
    }
  }

  // toggle size and shape
  &__ui {
    position: relative;
    width: var(--tui-form-toggle-container-width);
    margin-left: var(--tui-form-toggle-text-offset);

    // the toggle background
    &:before {
      display: block;
      height: var(--tui-form-toggle-container-height);
      border-radius: var(--tui-form-toggle-container-radius);
      transition: background-color var(--tui-transition-button-duration)
          var(--tui-transition-button-function),
        border-color var(--tui-transition-button-duration)
          var(--tui-transition-button-function);
      content: '';
    }

    // the toggle dot
    &:after {
      position: absolute;
      top: var(--tui-form-toggle-dot-offset);
      left: var(--tui-form-toggle-dot-offset);
      display: block;
      width: var(--tui-form-toggle-dot-size);
      height: var(--tui-form-toggle-dot-size);
      border-radius: 50%;
      box-shadow: var(--tui-shadow-2);
      transition: left var(--tui-transition-toggle-duration)
        var(--tui-transition-toggle-function);
      content: '';
    }

    &:hover,
    &:focus {
      cursor: pointer;
      &:before {
        background-color: var(--tui-form-toggle-off-bg-color-hover-focus);
        transition-duration: var(none--tui-transition-form-duration);
      }
    }

    &[disabled] {
      opacity: 0.4;
    }

    // toggled off

    // the toggle background
    &:before {
      background-color: var(--tui-form-toggle-off-bg-color);
      border: var(--tui-form-input-border-size) solid;
      border-color: var(--tui-form-toggle-border-color);
    }

    // the toggle dot
    &:after {
      background-color: var(--tui-form-toggle-dot-color);
    }

    &:hover,
    &:focus {
      &:before {
        background-color: var(--tui-form-toggle-off-bg-color-hover-focus);
      }
    }
  }

  // toggled off, via the button
  &__btn {
    &:hover,
    &:focus {
      ~ .tui-toggleSwitch__ui {
        &:before {
          background-color: var(--tui-form-toggle-off-bg-color-hover-focus);
        }
      }
    }
  }

  // toggled on
  &__ui.tui-toggleSwitch__ui--aria-pressed {
    // the dot
    &:after {
      right: var(--tui-form-toggle-dot-offset);
      left: auto;
    }

    // the toggle background
    &:before {
      background-color: var(--tui-form-toggle-on-bg-color);
      border-color: var(--tui-form-toggle-on-border-color);
    }

    &:hover,
    &:focus {
      &:before {
        background-color: var(--tui-form-toggle-on-bg-color-hover-focus);
      }
    }
  }

  // toggled on, via the button
  &__btn {
    &:hover,
    &:focus {
      ~ .tui-toggleSwitch__ui.tui-toggleSwitch__ui--aria-pressed {
        &:before {
          background-color: var(--tui-form-toggle-on-bg-color-hover-focus);
        }
      }
    }
  }

  // toggle on the left, text on the right
  &--left {
    .tui-toggleSwitch__ui {
      order: 1;
      margin-right: var(--tui-form-toggle-text-offset);
      margin-left: 0;
    }

    .tui-toggleSwitch__btn {
      order: 2;
    }

    .tui-toggleSwitch__icon {
      order: 3;
    }
  }
}
</style>
