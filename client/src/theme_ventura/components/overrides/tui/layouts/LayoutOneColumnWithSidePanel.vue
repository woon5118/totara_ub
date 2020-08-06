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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-layoutOneColumnContentWithSidepanel {
  &--fullSidePanel {
    > .tui-responsive > .tui-grid > .tui-grid-item {
      border-left: none;
    }
  }

  // Prevents the button edges from being hidden which would prevent the user from selecting the button again
  &--onSmallScreen {
    > .tui-responsive > .tui-grid > .tui-grid-item {
      .tui-sidepanel {
        overflow: visible;
        &--closed {
          .tui-sidepanel__inner {
            overflow: hidden;
          }
        }
      }
    }
  }
}
</style>
