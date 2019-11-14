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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-dataTableRow {
  position: relative;
  display: flex;
  flex-direction: column;
  padding-top: var(--tui-gap-3);
  padding-bottom: var(--tui-gap-3);
  background: var(--tui-datatable-row-bg-color);
  border-top: 1px solid var(--tui-datatable-row-border-color);

  &:first-child {
    border-top: var(--tui-border-width-normal) solid
      var(--tui-datatable-row-first-border-color);
  }

  &:last-child {
    border-bottom: 1px solid var(--tui-datatable-row-border-color);
  }

  &.tui-focusWithin,
  &:active,
  &:hover {
    background: var(--tui-datatable-row-bg-color-focus);
  }

  &--borderTopFirstOff {
    &:first-child {
      border-top: none;
    }
  }

  &--borderBottomLastOff {
    &:last-child {
      border-bottom: none;
    }
  }

  &--borderSeparatorOff:not(:first-child) {
    border-top: none;
  }

  &--selected {
    background: var(--tui-datatable-row-bg-color-active);

    &:hover {
      background: var(--tui-datatable-row-bg-color-focus);
    }
  }

  &--colorOdd:not(&--selected) {
    &:nth-child(odd) {
      background: var(--tui-datatable-row-bg-color-odd);

      &.tui-focusWithin,
      &:hover {
        background: var(--tui-datatable-row-bg-color-focus);
      }
    }
  }

  &--hoverOff {
    &.tui-focusWithin,
    &:active,
    &:hover {
      background: var(--tui-datatable-row-bg-color);
    }
  }

  &--hoverOff&--colorOdd {
    &:nth-child(odd) {
      &.tui-focusWithin,
      &:hover {
        background: var(--tui-datatable-row-bg-color-odd);
      }
    }
  }

  &--inGroup {
    &:first-child {
      border-top: none;
    }
    &:last-child {
      border-bottom: none;
    }
    &:nth-child(odd) {
      background: none;
    }
    &:hover {
      background: none;
    }
  }

  &--disabled {
    color: var(--tui-color-neutral-6);
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableRow {
    flex-direction: row;
    padding-right: var(--tui-gap-1);
    padding-left: var(--tui-gap-1);

    & > * + * {
      padding-left: var(--tui-gap-4);
    }

    &--inGroup {
      border-top: none;
      &:last-child {
        border-bottom: none;
      }
    }
  }
}
</style>
