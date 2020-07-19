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
.tui-sidePanelNavLinkItem {
  display: flex;
  align-items: center;
  color: var(--tui-side-panel-nav-item-text-color);
  background: var(--tui-side-panel-nav-item-bg-color);
  border-color: var(--tui-side-panel-nav-item-border-color);

  // Item li
  &.tui-focusWithin,
  &:hover {
    color: var(--tui-side-panel-nav-item-text-color-focus);
    background: var(--tui-side-panel-nav-item-bg-color-focus);
    border-color: var(--tui-side-panel-nav-item-border-color-focus);
  }

  &__side {
    margin-left: auto;
    padding-right: var(--tui-gap-4);
  }

  // Item link
  &__action {
    flex-grow: 1;
    padding: var(--tui-gap-2) var(--tui-gap-4);
    color: inherit;
    line-height: 1;
    -ms-word-break: break-all;
    word-break: break-word;

    &:hover,
    &:focus,
    &:focus:hover {
      color: inherit;
    }
  }

  &--active {
    color: var(--tui-side-panel-nav-item-text-color-selected);
    background: var(--tui-side-panel-nav-item-bg-color-selected);
    border-color: var(--tui-side-panel-nav-item-border-color-selected);

    &.tui-focusWithin,
    &:hover {
      color: var(--tui-side-panel-nav-item-text-color-selected);
      background: var(--tui-side-panel-nav-item-bg-color-selected);
      border-color: var(--tui-side-panel-nav-item-border-color-selected);
    }
  }
}
</style>
