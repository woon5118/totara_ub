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
  --tui-card-border-radius: var(--tui-border-radius-normal);
}

.tui-card {
  display: flex;
  border: 1px solid var(--tui-card-border-color);
  border-radius: var(--tui-card-border-radius);
  outline: none;

  &--noBorder {
    border: none;
    &:focus,
    &:hover {
      border: none;
    }
  }

  &--hasShadow {
    box-shadow: var(--tui-shadow-2);
  }

  &--clickable {
    transition: box-shadow var(--tui-transition-form-function)
      var(--tui-transition-form-duration);
  }

  &--clickable:hover,
  &--clickable:focus {
    box-shadow: var(--tui-shadow-2);
    cursor: pointer;
  }

  &.tui-focusWithin {
    box-shadow: var(--tui-shadow-2);
  }
}
</style>
