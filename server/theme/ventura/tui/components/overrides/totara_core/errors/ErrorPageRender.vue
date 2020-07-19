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
  @package theme_ventura
-->

<style lang="scss">
.tui-errorDisplay {
  padding: var(--tui-gap-8);
  background: var(--tui-color-neutral-3);
  border-radius: 4px;

  &__title {
    margin-top: 0;
  }

  &__buttons {
    margin-bottom: var(--tui-gap-4);
  }

  &__buttons:last-child {
    margin-bottom: 0;
  }

  &__detailsToggle {
    text-decoration: none;

    &:hover,
    &:focus {
      text-decoration: none;
    }
  }

  &__detailsPre {
    margin-top: var(--tui-gap-2);
    margin-bottom: 0;
    padding: var(--tui-gap-4);
    background-color: var(--tui-color-neutral-1);
    border-radius: 4px;
  }
}
</style>
