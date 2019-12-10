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
.tui-dropdown {
  position: relative;

  &__menu {
    position: absolute;
    z-index: var(--tui-zindex-dropdown-menu);
    display: none;
    width: 100%;
    min-width: 200px;
    max-height: 50vh;
    overflow-y: auto;
    background-color: var(--tui-dropdown-menu-bg-color);
    background-clip: padding-box;
    border: 1px solid var(--tui-dropdown-menu-border-color);
    border-radius: 4px;
    box-shadow: var(--tui-shadow-2);
  }

  &__content {
    display: flex;
    flex-direction: column;
    padding: 0.1rem 0.2rem;
    &--separator > * + * {
      border-top: 1px solid var(--tui-dropdown-menu-border-color);
    }
  }

  &--open {
    .tui-dropdown__menu {
      display: block;
    }
  }

  &--disabled {
    color: var(--tui-dropdown-item-text-color-disabled);
    cursor: not-allowed;
    .tui-dropdown__trigger {
      pointer-events: none;
    }
  }

  &--top-right {
    .tui-dropdown__menu {
      top: auto;
      right: 0;
      bottom: 100%;
      left: auto;
    }
  }

  &--top-left {
    .tui-dropdown__menu {
      top: auto;
      right: auto;
      bottom: 100%;
      left: 0;
    }
  }

  &--bottom-left {
    .tui-dropdown__menu {
      right: auto;
      left: 0;
    }
  }

  &--bottom-right {
    .tui-dropdown__menu {
      right: 0;
      left: auto;
    }
  }
}
</style>
