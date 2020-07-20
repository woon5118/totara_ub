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
.tui-competencyDetailAssignment {
  &__bar {
    padding: var(--tui-gap-4);
    background: var(--tui-color-neutral-3);

    &-filter.tui-selectFilter {
      .tui-select {
        max-width: initial;
      }
    }
  }

  &__actions {
    padding: var(--tui-gap-4) var(--tui-gap-2);
    text-align: right;
  }

  .tui-grid--stacked {
    & > * + * {
      justify-content: flex-start;
      padding-top: var(--tui-gap-4);
    }
  }

  &__level {
    display: flex;
    align-items: center;

    &-header {
      display: flex;
      margin: 0;
      @include tui-font-body-small();
    }

    &-infoBtn {
      padding-left: var(--tui-gap-1);
    }

    &-text {
      @include tui-font-heading-label-small();
    }

    &-wrap {
      @include tui-font-body-small();
      padding-left: var(--tui-gap-2);
      border-style: solid;
      border-width: 0 0 0 var(--tui-border-width-thick);

      .dir-rtl & {
        padding-right: var(--tui-gap-2);
        border-width: 0 var(--tui-border-width-thick) 0 0;
      }

      &-pending {
        border-color: var(--tui-progresstracker-color-pending);
      }

      &-complete {
        border-color: var(--tui-progresstracker-color-complete);
      }

      &-achieved {
        border-color: var(--tui-progresstracker-color-achieved);
      }
    }
  }

  &__status {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    &-text {
      margin-left: var(--tui-gap-2);
      @include tui-font-heading-small();

      .dir-rtl & {
        margin: 0 var(--tui-gap-2) 0 0;
      }

      &-complete {
        margin-left: var(--tui-gap-1);

        .dir-rtl & {
          margin: 0 var(--tui-gap-1) 0 0;
        }
      }
    }
  }
}
</style>
