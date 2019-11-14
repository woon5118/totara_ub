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
:root {
  --tui-dropdown-item-margin: 0.1rem;
}

.tui-dropdownItem {
  width: 100%;
  margin: var(--tui-dropdown-item-margin) 0;
  padding: var(--tui-gap-2) var(--tui-gap-4);
  overflow: hidden;
  color: var(--tui-dropdown-item-text-color);
  font-size: var(--tui-font-size-16);
  line-height: 1.4;
  white-space: nowrap;
  text-overflow: ellipsis;
  cursor: pointer;

  &:hover {
    color: var(--tui-dropdown-item-text-color-focus);
    text-decoration: none;
    background-color: var(--tui-dropdown-item-bg-color-focus);
    outline: 0;
  }

  &:focus {
    @include tui-focus();
    text-decoration: none;
    background-color: var(--tui-dropdown-item-bg-color-focus);
  }

  &--disabled,
  &--disabled:hover,
  &--disabled:focus {
    color: var(--tui-btn-text-color-disabled);
    background-color: var(--tui-btn-bg-color-disabled);
    cursor: not-allowed;
  }

  &--disabled:active {
    pointer-events: none;
  }

  &--small {
    padding: var(--tui-gap-1) var(--tui-gap-2);
    font-size: var(--tui-font-size-14);
  }

  &--paddingless {
    padding: 0;
  }
}
</style>
