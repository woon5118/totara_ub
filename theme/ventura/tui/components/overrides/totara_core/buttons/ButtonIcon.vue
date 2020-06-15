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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-iconBtn {
  @extend .tui-formBtn;

  display: inline-block;
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

  &--toggle {
    padding: 0 var(--tui-gap-2);
    color: var(--tui-btn-toggle-text-color);
    background: var(--tui-btn-toggle-bg-color);
    border-color: var(--tui-btn-toggle-border-color);

    &:first-child {
      border-radius: var(--tui-btn-radius) 0 0 var(--tui-btn-radius);
    }

    &:last-child,
    &:last-child:active {
      border-left: 0;
      border-radius: 0 var(--tui-btn-radius) var(--tui-btn-radius) 0;
    }

    .flex-icon {
      font-size: var(--tui-font-size-14);
    }

    &[disabled] {
      color: var(--tui-btn-toggle-text-color-disabled);
      background: var(--tui-btn-toggle-bg-color-disabled);
      border-color: var(--tui-btn-toggle-border-color-disabled);
      &:focus,
      &:hover,
      &:active,
      &:active:focus,
      &:active:hover {
        color: var(--tui-btn-toggle-text-color-disabled);
        background: var(--tui-btn-toggle-bg-color-disabled);
        border-color: var(--tui-btn-toggle-border-color-disabled);
      }
    }

    &:focus,
    &:hover {
      color: var(--tui-btn-toggle-text-color-focus);
      background: var(--tui-btn-toggle-bg-color-focus);
      border-color: var(--tui-btn-toggle-border-color-focus);
    }

    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-btn-toggle-text-color-active);
      background: var(--tui-btn-toggle-bg-color-active);
      border-color: var(--tui-btn-toggle-border-color-active);
    }
  }

  &--toggle&--selected {
    color: var(--tui-btn-toggle-text-color-selected);
    background: var(--tui-btn-toggle-bg-color-selected);
    border-color: var(--tui-btn-toggle-border-color-selected);
    box-shadow: none;
    cursor: default;

    &[disabled] {
      color: var(--tui-btn-toggle-text-color-disabled);
      background: var(--tui-btn-toggle-bg-color-disabled);
      border-color: var(--tui-btn-toggle-border-color-disabled);

      &:active,
      &:active:focus,
      &:active:hover {
        color: var(--tui-btn-toggle-text-color-disabled);
        background: var(--tui-btn-toggle-bg-color-disabled);
        border-color: var(--tui-btn-toggle-border-color-disabled);
      }
    }

    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-btn-toggle-text-color-selected);
      background: var(--tui-btn-toggle-bg-color-selected);
      border-color: var(--tui-btn-toggle-border-color-selected);
      box-shadow: none;
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
