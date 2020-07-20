<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-filterBar {
  display: flex;
  flex-direction: column;
  padding: 0 var(--tui-gap-3);

  &--hasTop {
    padding-top: var(--tui-gap-4);
    border-top: var(--tui-border-width-normal) solid
      var(--tui-filter-bar-border-color);
  }

  &--hasBottom {
    padding-bottom: var(--tui-gap-4);
    border-bottom: var(--tui-border-width-normal) solid
      var(--tui-filter-bar-border-color);
  }

  &__heading {
    @include sr-only();
  }

  &__toggle {
    display: flex;
    justify-content: center;

    &-btn {
      margin: var(--tui-gap-2) 0;
    }
  }

  &__filters {
    $stacked: #{&}--stacked;
    display: flex;
    flex-grow: 1;

    &-left,
    &-right {
      display: flex;
      flex-shrink: 0;

      > * {
        flex-shrink: 0;
      }

      > * + * {
        margin-left: var(--tui-gap-4);
      }

      // make slider icon closer to next filter item
      > .tui-filterBar__filters-icon + * {
        margin-left: var(--tui-gap-2);
      }

      #{$stacked} & {
        flex-direction: column;
        margin-left: 0;

        .tui-formLabel {
          @include tui-font-heading-label-small();
        }

        > * {
          margin-top: var(--tui-gap-2);
          margin-left: 0;
        }
      }
    }

    &-right {
      flex-grow: 1;
      justify-content: flex-end;
      margin-left: var(--tui-gap-4);
    }

    &-icon {
      display: flex;
      align-items: center;
    }

    &-iconSlider {
      display: flex;
      margin: auto 0;
    }

    &--stacked {
      flex-direction: column;
    }
  }
}
</style>
