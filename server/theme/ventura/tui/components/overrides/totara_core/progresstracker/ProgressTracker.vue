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
$progress-tracker-line-sm: 100px !default;
$progress-tracker-line-md: 200px !default;
$progress-tracker-line-lg: 250px !default;
$progress-tracker-half-circle: 14px !default;

.tui-progressTrackerItems {
  display: flex;
  justify-content: center;
  margin: 0;
  list-style: none;

  // Edge case
  .tui-popoverPositioner {
    width: 250px;
  }

  // Line styles
  & > * + * {
    &.tui-progressTrackerItem {
      .tui-progressTrackerItem__content::before {
        position: absolute;
        top: $progress-tracker-half-circle;
        left: -50%;
        display: block;
        width: 100%;
        border-style: solid;
        border-width: var(--tui-border-width-thin) 0 0 0;
        content: '';
      }

      &--pending {
        .tui-progressTrackerItem__content::before {
          border-color: var(--tui-progresstracker-color-pending);
          border-style: dotted;
        }
      }

      &--complete {
        .tui-progressTrackerItem__content::before {
          top: ($progress-tracker-half-circle - 1);
          border-color: var(--tui-progresstracker-color-complete);
          border-width: var(--tui-border-width-thick) 0 0 0;
        }
      }

      &--achieved {
        .tui-progressTrackerItem__content::before {
          top: ($progress-tracker-half-circle - 1);
          border-color: var(--tui-progresstracker-color-achieved);
          border-width: var(--tui-border-width-thick) 0 0 0;
        }
      }
    }
  }
  &--vertical {
    & > * + * {
      &.tui-progressTrackerItem {
        .tui-progressTrackerItem__content::before {
          top: -50%;
          left: ($progress-tracker-half-circle - 1);
          width: 0;
          height: 100%;
          border-width: 0 0 0 var(--tui-border-width-thick);
        }
      }
    }
  }

  .tui-progressTrackerItem {
    position: relative;
    flex-shrink: 0;

    &--small {
      width: $progress-tracker-line-sm;
    }

    &--medium {
      width: $progress-tracker-line-md;
    }

    &--large {
      width: $progress-tracker-line-lg;
    }

    &__content {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
    }

    &__label {
      position: relative;
      width: 100%;
      margin: 0 auto;
      padding: var(--tui-gap-2);
      text-align: center;

      &-current {
        .tui-formBtn {
          color: var(--tui-color-state-active);
          font-weight: bold;
        }
      }
    }

    &__status,
    &__target {
      @include sr-only();
    }

    // Spacing after for overflow detector
    :last-child {
      &--small {
        width: ($progress-tracker-line-sm * 2);
        padding-right: $progress-tracker-line-sm;
      }

      &--medium {
        width: ($progress-tracker-line-md * 2);
        padding-right: $progress-tracker-line-md;
      }

      &--large {
        width: ($progress-tracker-line-lg * 2);
        padding-right: $progress-tracker-line-lg;
      }
    }
  }

  &--vertical {
    flex-direction: column;

    .tui-progressTrackerItem {
      width: auto;
      height: 80px;

      &__content {
        flex-direction: row;
        height: 100%;
      }

      &__label {
        margin: 0;
        padding: var(--tui-gap-1) var(--tui-gap-2);
        text-align: left;
      }
    }
  }
}
</style>
