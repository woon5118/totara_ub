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
.tui-checkboxButton {
  $block: #{&};
  position: relative;
  display: flex;

  &__input {
    position: absolute;
    opacity: 0;
  }

  &__label {
    @include tui-font-body-small();
    display: flex;
    flex-grow: 1;
    margin: 0;
    padding: var(--tui-gap-2);
    color: var(--tui-btn-checkbox-text-color);
    font-weight: normal;
    border-radius: 6px;
    cursor: pointer;

    &:hover {
      color: var(--tui-btn-checkbox-text-color-focus);
      background: var(--tui-btn-checkbox-bg-color-hover);
    }

    &:active:hover,
    &:active {
      color: var(--tui-btn-checkbox-text-color-active);
    }
  }

  &__deselectIcon {
    margin: auto 0 auto auto;
    color: var(--tui-btn-checkbox-text-color-selected);
    visibility: hidden;
  }

  &__input:checked ~ &__label {
    color: var(--tui-btn-checkbox-text-color-selected);
    background: var(--tui-btn-checkbox-bg-color-selected);

    #{$block}__deselectIcon {
      visibility: visible;
    }

    &:hover {
      color: var(--tui-btn-checkbox-text-color-focus);
      background: var(--tui-btn-checkbox-bg-color-hover);

      #{$block}__deselectIcon {
        color: var(--tui-btn-checkbox-text-color-focus);
        visibility: visible;
      }
    }

    &:active:hover,
    &:active {
      color: var(--tui-btn-checkbox-text-color-active);
      background: var(--tui-btn-checkbox-bg-color-hover);

      #{$block}__deselectIcon {
        color: var(--tui-btn-checkbox-text-color-active);
        visibility: visible;
      }
    }
  }

  &__input:focus ~ &__label {
    @include tui-focus();
  }

  &__input:focus:checked ~ &__label {
    @include tui-focus();

    #{$block}__deselectIcon {
      color: var(--tui-btn-checkbox-text-color-selected);
      visibility: visible;
    }
  }
}
</style>
