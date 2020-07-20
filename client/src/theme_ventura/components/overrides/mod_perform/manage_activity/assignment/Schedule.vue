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
  width: max-content;

  &__heading {
    @include tui-font-heading-small;
  }

  &__sectionHeading {
    @include tui-font-heading-label;
  }

  &__data {
    margin: var(--tui-gap-5) var(--tui-gap-2) var(--tui-gap-3);
    background: var(--tui-color-neutral-2);
    border: 1px solid var(--tui-color-neutral-5);
    border-radius: 6px;
  }

  &__additional_settings {
    margin-top: var(--tui-gap-5);
  }

  &__action {
    margin-top: var(--tui-gap-5);
  }

  &__radio {
    &-disabled {
      @include tui-font-body-disabled;
    }
  }
}
</style>
