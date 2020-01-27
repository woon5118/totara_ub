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
.tui-progressTrackerCircle {
  $pending: #{&}--pending;
  $complete: #{&}--complete;
  $achieved: #{&}--achieved;
  $target: #{&}--target;

  &__outer {
    z-index: 2;
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: calc(var(--tui-gap-7) + 1px);
    height: calc(var(--tui-gap-7) + 1px);
    border: var(--tui-border-width-normal) transparent dotted;
    border-radius: 50%;

    // Pending target
    &#{$pending}&#{$target} {
      border-color: var(--tui-progresstracker-color-pending);
    }

    // Achieved target
    &#{$target}&#{$achieved} {
      background: var(--tui-progresstracker-container-bg-color);
      border-color: var(--tui-progresstracker-color-achieved);
      border-style: solid;
    }
  }

  &__middle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: calc(var(--tui-gap-4) + 1px);
    height: calc(var(--tui-gap-4) + 1px);
    background: transparent;
    border: var(--tui-border-width-thin) solid transparent;
    border-radius: 50%;

    // Pending
    #{$pending} & {
      background: var(--tui-progresstracker-color-pending);
    }

    // Pending target
    #{$pending}#{$target} & {
      background: transparent;
    }

    // Complete
    #{$complete} & {
      background: var(--tui-progresstracker-color-complete);
    }

    // Achieved
    #{$achieved} & {
      background: var(--tui-progresstracker-color-achieved);
    }
  }

  &__inner {
    width: calc(var(--tui-gap-2) + 1px);
    height: calc(var(--tui-gap-2) + 1px);
    background: var(--tui-progresstracker-container-bg-color);
    border: var(--tui-border-width-thin) solid
      var(--tui-progresstracker-container-bg-color);
    border-radius: 50%;

    // Pending
    #{$pending} & {
      border-color: var(--tui-progresstracker-container-bg-color);
    }

    // Pending target
    #{$pending}#{$target} & {
      border-color: var(--tui-progresstracker-color-pending);
    }

    // Achieved
    #{$achieved} & {
      border-color: var(--tui-progresstracker-container-bg-color);
    }
  }
}
</style>
