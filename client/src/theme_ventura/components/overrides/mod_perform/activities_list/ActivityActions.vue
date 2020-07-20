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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module mod_perform
-->

<style lang="scss">
.tui-performActivityActions {
  &__actionIcons {
    display: flex;
    justify-content: flex-start;

    &__link {
      padding-right: var(--tui-gap-1);
    }
  }
}

@media screen and (min-width: $tui-screen-xs) {
  .tui-performActivityActions {
    &__actionIcons {
      justify-content: flex-end;
    }
  }
}
</style>
