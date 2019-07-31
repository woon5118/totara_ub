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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-emptySpacesPage {
  display: flex;
  flex-direction: column;

  // Overriding the height here.
  .tui-responsive {
    flex-grow: 1;
    width: 100%;
    height: 100%;
  }

  &__content {
    padding-right: var(--tui-gap-8);

    &__header {
      margin-top: calc(var(--tui-gap-12) * 2);
      margin-bottom: var(--tui-gap-12);
    }

    &__horizontalLine {
      margin: 0;
    }

    &__recommendedSpaces {
      margin-top: var(--tui-gap-8);
    }
  }
}
</style>
