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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
-->

<style lang="scss">
.tui-assignmentScheduleToggles {
  display: flex;
  flex-wrap: wrap;

  &__group {
    display: flex;
    flex-wrap: wrap;
    margin-right: var(--tui-gap-4);

    &:last-child {
      margin-right: 0;
    }
  }

  &__item {
    margin-right: var(--tui-gap-4);

    &-header {
      margin: var(--tui-gap-4) 0 0;
      @include tui-font-heading-label;
    }

    &-toggle {
      margin-top: var(--tui-gap-4);
    }
  }
}

@media (min-width: $tui-screen-sm) {
  .tui-assignmentScheduleToggles {
    &__group {
      border-right: var(--tui-border-width-thin) solid
        var(--tui-color-neutral-6);

      &:last-child {
        border-right: none;
      }
    }

    &__item {
      &-header {
        margin: 0;
      }
    }
  }
}
</style>
