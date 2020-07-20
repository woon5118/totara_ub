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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-performAssignmentScheduleRelativeDateSelector {
  display: inline-flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-content: space-between;
  align-items: baseline; // Before changing this to center or something else, check the validation errors line up ok.
  justify-content: flex-start;

  & > *:not(:last-child) {
    margin-top: var(--tui-gap-1);
    margin-right: var(--tui-gap-1);
  }

  &__count {
    max-width: 5rem;
  }

  &__unit,
  &__direction {
    max-width: 10rem;
  }

  &__reference-date {
    display: flex;
    flex-direction: row;
    &-customSettings {
      min-width: 20rem;
      margin-left: var(--tui-gap-1);
    }
  }

  .tui-formFieldError__inner {
    min-width: 20rem; // Should find a way to handle this better.
    margin-top: var(--tui-gap-1);
  }
}
</style>
