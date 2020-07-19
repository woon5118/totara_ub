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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-competencySelfAssignment {
  &__table {
    & > * + * {
      margin-top: var(--tui-gap-2);
    }
  }

  &__tableExpand {
    & > * + * {
      margin: var(--tui-gap-2) 0 0;
    }

    &-header {
      @include tui-font-heading-small();
    }

    &-subHeader {
      @include tui-font-heading-x-small();
    }
  }

  &__actions {
    justify-content: space-between;
    margin-bottom: var(--tui-gap-5);
  }

  &__header {
    margin: 0;
    @include tui-font-heading-small();
  }

  &__table {
    margin-bottom: var(--tui-gap-5);
  }
}
</style>
