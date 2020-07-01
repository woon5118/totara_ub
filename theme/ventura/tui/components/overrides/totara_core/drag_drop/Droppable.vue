<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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
  @package totara_core
-->

<style lang="scss">
.tui-droppable {
  &__dropLine {
    z-index: var(--tui-zindex-draggable);
    background: var(--tui-color-secondary);
    pointer-events: none;

    &--vertical {
      height: var(--tui-border-width-normal);
      margin-top: calc(var(--tui-border-width-normal) * -0.5);
    }

    &--horizontal {
      width: var(--tui-border-width-normal);
      margin-left: calc(var(--tui-border-width-normal) * -0.5);
    }
  }
}
[data-tui-droppable-location-indicator] {
  background-color: var(--tui-color-neutral-5);
}
</style>
