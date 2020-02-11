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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-bulkManualRatingCell {
  &__newRating {
    @media (min-width: $tui-screen-sm) {
      display: flex;
      flex-direction: row-reverse;
    }
  }

  &__rating {
    display: flex;
    align-items: center;
    margin-right: var(--tui-gap-2);

    &-hasComment {
      margin-left: var(--tui-gap-1);
    }

    &-noValue {
      @include tui-font-hint;
    }

    &-valueName {
      @include tui-font-heading-label;
    }
  }
}
</style>
