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
.tui-actionCard {
  flex-direction: column;
  padding: var(--tui-gap-4);

  &__body {
    display: flex;
    flex-grow: 1;
    flex-shrink: 1;
    flex-wrap: wrap;
    align-items: center;
  }

  &__action {
    display: flex;
    flex-shrink: 0;
    align-items: normal;
    padding: var(--tui-gap-4) 0 0;
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-actionCard {
    flex-direction: row;
    &--stacked {
      flex-direction: column;
    }

    &:not(&--stacked) &__action {
      align-items: center;
      padding: 0 0 0 var(--tui-gap-4);
    }
  }
}
</style>
