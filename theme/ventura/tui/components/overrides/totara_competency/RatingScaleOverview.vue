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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-competencyRatingScaleOverview {
  &__title {
    @include tui-font-heading-x-small;
    margin-top: 0;
    margin-bottom: var(--tui-gap-2);
  }

  &__list {
    margin: 0;

    &-item {
      display: flex;

      &:not(:last-child) {
        padding-bottom: var(--tui-gap-2);
      }
    }
  }

  &__proficientStatus {
    min-width: var(--tui-gap-8);
  }

  &__scaleValue {
    display: flex;
    flex-direction: column;

    &-name {
      @include tui-font-heading-label;
      margin: 0;
    }

    &-description {
      padding-top: var(--tui-gap-1);
      padding-left: var(--tui-gap-3);
    }
  }
}
</style>
