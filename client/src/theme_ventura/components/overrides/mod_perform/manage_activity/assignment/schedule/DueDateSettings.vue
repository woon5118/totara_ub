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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module mod_perform
-->

<style lang="scss">
.tui-performAssignmentDueDateSettings {
  &__fixed {
    .tui-radio__label {
      display: inline-flex;
      flex-wrap: wrap;
      align-items: baseline;

      @media (min-width: $tui-screen-sm) {
        flex-wrap: nowrap;
      }
    }

    div.tui-dateSelector__date,
    div.tui-dateSelector__time {
      margin: var(--tui-gap-2) var(--tui-gap-2) 0;

      @media (min-width: $tui-screen-sm) {
        margin-top: 0;
      }
    }
  }
}
</style>
