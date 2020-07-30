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
  @package theme_ventura
-->

<style lang="scss">
.tui-performUserActivitiesExternalUserSelector {
  & .tui-repeater__row {
    flex-direction: column;

    & > .tui-formField + .tui-formField {
      margin: var(--tui-gap-2) 0 0 0;
    }
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-performUserActivitiesExternalUserSelector {
    & .tui-repeater__row {
      flex-direction: row;

      & > .tui-formField + .tui-formField {
        margin: 0 0 0 var(--tui-gap-2);
      }
    }
  }
}
</style>
