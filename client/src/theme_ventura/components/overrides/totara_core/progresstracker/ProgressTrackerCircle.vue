<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
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
