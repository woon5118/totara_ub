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
.tui-tag {
  display: inline-flex;
  flex-shrink: 0;
  align-items: center;
  height: 24px;
  white-space: nowrap;
  background-color: var(--tui-tag-bg-color);
  border: 1px solid var(--tui-tag-border-color);
  border-radius: 12px;

  &__content {
    padding: 0 var(--tui-gap-2);
    color: var(--tui-tag-active-color);
    font-size: 13px;

    &--withButton {
      padding-right: 0;
      color: var(--tui-tag-button-color);
    }
  }

  &--primary {
    text-transform: uppercase;
    background-color: var(--tui-color-state);
  }

  &--primary &__content {
    color: var(--tui-color-neutral-1);
  }
}
</style>
