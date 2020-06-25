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
.tui-competencySummaryAchievementConfiguration {
  & > * + * {
    margin-top: var(--tui-gap-2);
  }

  &__aggregation {
    margin-top: var(--tui-gap-4);
    &-label {
      @include tui-font-heavy();
      margin-right: var(--tui-gap-2);
    }
  }

  &__noPaths {
    padding: var(--tui-font-size-8);
    font-style: italic;
  }

  &__pathGroup {
    flex-grow: 1;
    padding: var(--tui-gap-4);

    & > * + * {
      &.tui-grid {
        margin-top: var(--tui-gap-4);
        padding-top: var(--tui-gap-4);
        border-top: var(--tui-border-width-thin) solid
          var(--tui-card-border-color);
      }
    }
  }

  &__scaleValue {
    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-or {
      width: 50%;
      margin: 0;
    }
  }
}
</style>
