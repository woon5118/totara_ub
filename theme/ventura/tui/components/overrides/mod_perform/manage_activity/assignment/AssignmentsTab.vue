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
.tui-performManageActivityAssignmentsForm {
  &__heading {
    align-items: center;
    margin-top: var(--tui-gap-2); // Check the content tab before changing this.

    &-title {
      @include tui-font-heading-small();
    }

    &-buttons {
      display: flex;
    }

    &-dropdown {
      @media (min-width: $tui-screen-xs) {
        margin-left: auto;
      }
      @media (max-width: $tui-screen-xs) {
        margin-top: var(--tui-gap-2);
      }
    }
  }

  &__table {
    margin-top: var(--tui-gap-4);
  }

  &__summary {
    display: flex;
    margin-top: var(--tui-gap-4);

    &-count {
      display: flex;
      flex-direction: column;
      justify-content: center;
      margin-right: var(--tui-gap-4);
    }
  }
}
</style>
