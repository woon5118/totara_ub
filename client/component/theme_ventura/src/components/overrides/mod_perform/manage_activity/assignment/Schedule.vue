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
.tui-performAssignmentSchedule {
  & > * + * {
    margin-top: var(--tui-gap-8);
  }

  &__heading {
    margin: var(--tui-gap-12) 0 0;
    @include tui-font-heading-small;
  }

  &__form {
    & > * + * {
      margin-top: var(--tui-gap-4);
    }

    &-section {
      padding: var(--tui-gap-8);
      background: var(--tui-color-neutral-3);
    }
  }

  .tui-performAssignmentSchedule__form {
    &-additional {
      margin-top: var(--tui-gap-12);
      margin-left: var(--tui-gap-8);
    }

    &-buttons {
      margin-top: var(--tui-gap-8);
      margin-left: var(--tui-gap-8);
    }
  }
}
</style>
