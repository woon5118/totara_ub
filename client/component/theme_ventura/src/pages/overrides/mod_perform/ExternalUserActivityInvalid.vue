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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @module mod_perform
-->

<style lang="scss">
.tui-performUserActivityInvalid {
  @include tui-font-body();

  margin: var(--tui-gap-8) 0 0;

  & > * + * {
    margin-top: var(--tui-gap-6);
  }

  &__header {
    @include tui-font-heading-medium();
  }
}
</style>
