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
.tui-pathwayLearningPlanAchievement {
  &__empty {
    font-size: var(--tui-font-size-16);
    @include tui-font-hint();
  }

  &__comment {
    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-body {
      margin-top: var(--tui-gap-2);
    }
  }

  &__summary {
    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-body {
      margin-top: var(--tui-gap-4);
    }

    &-button {
      margin-top: var(--tui-gap-4);
    }
  }

  &__title {
    margin: 0;
    @include tui-font-heading-x-small();
  }

  &__noValue {
    margin-top: var(--tui-gap-2);
    @include tui-font-hint();
  }

  &__rater {
    display: flex;

    &-name {
      margin: auto 0 auto var(--tui-gap-2);
    }
  }

  &__rating {
    display: flex;

    &-value {
      margin-right: var(--tui-gap-1);
    }
  }

  &__value {
    margin-top: var(--tui-gap-2);

    &-title {
      @include tui-font-heavy();
    }
  }

  &__content {
    padding: var(--tui-gap-4);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: 6px;
  }
}
</style>
