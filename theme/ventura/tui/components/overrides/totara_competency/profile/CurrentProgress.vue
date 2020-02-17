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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-competencyProfileCurrentProgress {
  margin-bottom: var(--tui-gap-8);

  &__progress {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin: 0 0 var(--tui-gap-4) 0;
    padding: 0;
    list-style: none;

    @media (min-width: $tui-screen-xs) {
      justify-content: flex-start;
    }
  }

  &__userDetails {
    display: flex;
    flex-direction: column;

    & > :not(:last-child) {
      margin-bottom: var(--tui-gap-4);
    }
  }

  &__latestAchievement {
    $header-v-padding: var(--tui-gap-2);
    $header-height: calc(
      var(--tui-font-size-13) * 1.15 + var(--tui-border-width-normal) + #{$header-v-padding} *
        2
    );

    display: flex;
    flex-direction: column;
    margin-top: var(--tui-gap-10);
    padding: 0 var(--tui-gap-1);
    background: var(--tui-color-background);
    border: var(--tui-border-width-normal) solid var(--tui-color-primary);
    border-radius: 6px;

    &-header {
      max-width: 100%;
      height: $header-height;
      margin: calc(
          -1 * var(--tui-border-width-normal) - (#{$header-height} / 2)
        )
        auto calc(-1 * var(--tui-gap-2)) auto;
      padding: $header-v-padding var(--tui-gap-4);
      overflow: hidden;
      font-weight: bold;
      font-size: var(--tui-font-size-13);
      line-height: 1.15;
      white-space: nowrap;
      text-overflow: ellipsis;
      background: var(--tui-color-background);
      border: var(--tui-border-width-normal) solid var(--tui-color-primary);
      border-radius: 6px;
    }

    &-content {
      padding: var(--tui-gap-4) var(--tui-gap-6);
    }
  }
}
</style>
