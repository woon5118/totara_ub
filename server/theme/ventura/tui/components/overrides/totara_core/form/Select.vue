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
  --tui-select-icon-size: var(--tui-gap-1);
}

// Reset
.tui-select__input {
  display: inline-block;
  width: auto;
  max-width: none;
  height: auto;
  max-height: none;
  margin: 0;
  padding: 0;
  color: black;
  font: 400 13.3333px Arial;
  font-size: inherit;
  line-height: inherit;
  letter-spacing: normal;
  white-space: pre;
  text-align: start;
  text-transform: none;
  text-indent: 0;
  text-shadow: none;
  word-spacing: normal;
  background-color: white;
  border-color: rgb(169, 169, 169);
  border-style: solid;
  border-width: 1px;
  border-radius: 0;
  border-image: initial;
  box-shadow: none;
  cursor: default;
  transition-delay: 0s;
  transition-timing-function: ease;
  transition-duration: 0s;
  transition-property: all;
  text-rendering: auto;
  appearance: menulist;

  &[disabled] {
    color: rgb(61, 68, 75);
    background: rgb(218, 218, 218);
    cursor: default;
  }

  &[multiple] {
    height: auto;
    overflow-x: hidden;
    overflow-y: visible;
  }

  &:focus {
    border-color: rgb(218, 218, 218);
    outline-width: 3px;
    outline-style: auto;
    outline-color: Highlight;
    outline-color: -webkit-focus-ring-color;
    outline-offset: -2px;
    box-shadow: none;
    -moz-user-focus: normal;
  }
}

.tui-select {
  position: relative;
  display: flex;
  flex-grow: 1;
  min-width: 0;
  height: var(--tui-form-input-height);

  &::after {
    position: absolute;
    top: calc((var(--tui-form-input-height) - var(--tui-select-icon-size)) / 2);
    right: calc(
      (var(--tui-form-input-height) - var(--tui-select-icon-size) * 2) / 2
    );
    display: block;
    width: 0;
    height: 0;
    border: var(--tui-select-icon-size) solid transparent;
    border-top-color: var(--tui-form-input-text-color);
    content: '';
    pointer-events: none;
  }

  &--disabled::after {
    border-top-color: var(--tui-form-input-text-color-disabled);
  }

  &--multiple::after {
    display: none;
  }

  &--large {
    height: var(--tui-form-input-height-large);

    &::after {
      top: calc(
        (var(--tui-form-input-height-large) - var(--tui-select-icon-size)) / 2
      );
      right: calc(
        (var(--tui-form-input-height-large) - var(--tui-select-icon-size) * 2) /
          2
      );
    }
  }

  &__input {
    flex-grow: 1;
    box-sizing: border-box;
    width: 100%;
    min-width: 0;
    padding: 0 var(--tui-gap-6) 0 var(--tui-gap-1);
    color: var(--tui-form-input-text-color);
    font-size: var(--tui-form-input-font-size);
    background: var(--tui-form-input-bg-color);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-input-border-color);
    appearance: none;

    &[multiple] {
      height: auto;
    }

    &[disabled] {
      color: var(--tui-form-input-text-color-disabled);
      background: var(--tui-form-input-bg-color-disabled);
      border-color: var(--tui-form-input-border-color-disabled);
    }

    &:focus {
      background: var(--tui-form-input-bg-color-focus);
      border: var(--tui-form-input-border-size) solid;
      border-color: var(--tui-form-input-border-color-focus);
      outline: none;
      box-shadow: var(--tui-shadow-1);

      .tui-context-invalid & {
        background: var(--tui-form-input-bg-color-invalid-focus);
        border-color: var(--tui-form-input-border-color-invalid);
        box-shadow: var(--tui-shadow-1-invalid);
      }
    }

    // Drop select outline
    &:-moz-focusring {
      color: transparent;
      text-shadow: 0 0 0 #000;
    }

    // appearance: none; equivalent for IE
    &::-ms-expand {
      display: none;
    }

    // prevent weird styling after selecting value
    &::-ms-value {
      color: inherit;
      background-color: transparent;
    }

    & option {
      background: var(--tui-color-neutral-1);
    }

    .tui-context-invalid & {
      border-color: var(--tui-form-input-border-color-invalid);
    }
  }
}
</style>
