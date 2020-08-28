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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->
<style lang="scss">
.tui-workspacePrimaryAction {
  display: flex;
  width: 100%;

  @media (min-width: $tui-screen-sm) {
    justify-content: flex-end;
  }

  &--initialise {
    justify-content: center;
    padding: var(--tui-gap-2);
  }

  &__dropDown {
    width: 100%;

    @media (min-width: $tui-screen-sm) {
      // IE support - :(
      width: auto;
    }

    &__button {
      width: 100%;

      @media (min-width: $tui-screen-sm) {
        // IE support - :(
        width: auto;
      }
    }
  }

  &__button {
    width: 100%;

    @media (min-width: $tui-screen-sm) {
      // IE Support - :(
      width: auto;
    }
  }
}
</style>
