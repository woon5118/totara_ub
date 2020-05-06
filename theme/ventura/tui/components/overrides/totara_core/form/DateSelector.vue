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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-dateSelector {
  display: flex;
  flex-wrap: wrap;

  &__date {
    display: flex;
    margin: 0 var(--tui-gap-4) var(--tui-gap-2) 0;

    & > * {
      display: flex;
    }

    & > * + * {
      margin-left: var(--tui-gap-2);
    }

    &-day {
      width: 5rem;
    }

    &-month {
      width: 12rem;
    }

    &-year {
      width: 7rem;
    }
  }

  &__time {
    display: flex;
    width: calc((var(--tui-gap-2) * 2) + 24rem);
  }
}
</style>
