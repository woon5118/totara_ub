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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-layoutOneColumnWithSidepanel
  > .tui-responsive
  > .tui-grid
  > .tui-grid-item {
  transition: flex-basis ease-in 0.3s;

  &__heading {
    @include tui-font-heading-medium();
  }
}
</style>
