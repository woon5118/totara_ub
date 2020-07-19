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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-popoverPositioner {
  // note: position: absolute + z-index triggers a new stacking context
  // this allows us to escape from overflow: hidden.
  // because stacking contexts can be nested, this will still work correctly
  // even inside modals, which have a higher z-index
  position: absolute;
  top: 0;
  /*rtl:ignore*/
  left: 0;
  z-index: var(--tui-zindex-popover);
  width: auto;
  @include tui-font-body();

  &--transition-default {
    transition: opacity 0.2s;
  }

  &--transition-default-enter,
  &--transition-default-leave-to {
    opacity: 0;
  }
}
</style>
