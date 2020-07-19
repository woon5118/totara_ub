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
  @package theme_ventura
-->

<style lang="scss">
.tui-basket {
  @include tui-font-body();

  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  background-color: var(--tui-color-neutral-3);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  @media (min-width: $tui-screen-sm) {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }

  &__status,
  &__actions {
    display: flex;
    flex-shrink: 0;
    flex-wrap: wrap;
    align-items: center;
    padding: var(--tui-gap-2) var(--tui-gap-3);

    > .tui-formBtn--transparent {
      padding: var(--tui-gap-1);
    }

    > * + * {
      margin-left: var(--tui-gap-2);
    }
  }

  &__selected {
    font-weight: bold;
  }

  &__selectedCount {
    font-size: var(--tui-font-size-18);
  }
}
</style>
