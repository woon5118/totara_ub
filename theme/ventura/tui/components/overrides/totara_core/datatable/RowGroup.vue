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
  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-dataTableRowGroup {
  padding: 0 var(--tui-gap-2);
  border-top: 2px solid var(--tui-datatable-row-first-border-color);

  & + & {
    border-top: 1px solid var(--tui-datatable-row-border-color);
  }

  &:last-child {
    border-bottom: 1px solid var(--tui-datatable-row-border-color);
  }

  &.tui-focusWithin,
  &:active,
  &:hover {
    background: var(--tui-datatable-row-bg-color-focus);
  }

  &--selected {
    background: var(--tui-datatable-row-bg-color-active);

    &:hover {
      background: var(--tui-datatable-row-bg-color-focus);
    }
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableRowGroup {
    padding: 0;
  }
}
</style>
