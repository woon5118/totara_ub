<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Simon Chester <simon.chester@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
:root {
  // Size of radio circle
  --tui-form-radio-size: var(--tui-font-size-16);
  // Size of radio inner dot
  --tui-form-radio-dot-size: calc(var(--tui-form-radio-size) / 2);
  // Offset to center dot
  --tui-radio-dot-offset: calc(var(--tui-form-radio-size) / 4);
}

.tui-radio {
  position: relative;
  display: inline-flex;

  &__input {
    position: absolute;
    opacity: 0;
  }

  &__label {
    position: relative;
    margin: 0;
    padding-left: calc(var(--tui-form-radio-size) * 1.35);
    font-weight: normal;
    font-size: var(--tui-form-input-font-size);
    line-height: 1;

    &::before {
      position: absolute;
      top: 0;
      left: 0;
      display: block;
      width: var(--tui-form-radio-size);
      height: var(--tui-form-radio-size);
      background: var(--tui-form-radio-bg-color);
      border: var(--tui-form-input-border-size) solid;
      border-color: var(--tui-form-radio-border-color);
      border-radius: 50%;
      transition: border var(--tui-transition-form-function)
          var(--tui-transition-form-duration),
        box-shadow var(--tui-transition-form-function)
          var(--tui-transition-form-duration);
      content: '';
      pointer-events: none;
    }
  }

  &__input:disabled ~ &__label {
    color: var(--tui-form-input-text-color-disabled);
  }

  &__input:hover ~ &__label::before {
    background: var(--tui-form-radio-bg-color-hover);
  }

  &__input:focus ~ &__label::before {
    background: var(--tui-form-radio-bg-color-focus);
  }

  &__input:hover ~ &__label::before,
  &__input:focus ~ &__label::before {
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-radio-border-color-focus);
    box-shadow: var(--tui-shadow-1);
  }

  &__input:active:focus ~ &__label::before,
  &__input:active:hover ~ &__label::before,
  &__input:active ~ &__label::before {
    background: var(--tui-form-radio-bg-color-active);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-radio-border-color-active);
    box-shadow: var(--tui-shadow-1);
  }

  &__input:disabled:active ~ &__label::before,
  &__input:disabled ~ &__label::before {
    background: var(--tui-form-radio-bg-color-disabled);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-radio-border-color-disabled);
    box-shadow: none;
  }

  &__input ~ &__label::after {
    position: absolute;
    top: var(--tui-radio-dot-offset);
    left: var(--tui-radio-dot-offset);
    display: block;
    width: var(--tui-form-radio-dot-size);
    height: var(--tui-form-radio-dot-size);
    background-color: var(--tui-form-radio-dot-color);
    border-radius: 50%;
    opacity: 0;
    transition: opacity var(--tui-transition-form-function)
      var(--tui-transition-form-duration);
    content: '';
    pointer-events: none;
  }

  &__input:disabled ~ &__label::after {
    background-color: var(--tui-form-input-text-color-disabled);
  }

  &__input:checked ~ &__label::after {
    opacity: 1;
  }
}
</style>
