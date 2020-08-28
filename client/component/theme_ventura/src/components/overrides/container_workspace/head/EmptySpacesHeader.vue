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
.tui-emptySpacesHeader {
  display: flex;
  flex-direction: column;
  align-items: center;

  &__title {
    @include tui-font-heading-medium();
    margin: 0;
    margin-bottom: var(--tui-gap-12);
  }

  &__actionBox {
    display: flex;
    flex-direction: column;
    align-items: center;

    &__actionLink {
      margin-bottom: var(--tui-gap-4);
    }

    p {
      @include tui-font-body();
      margin: 0;
      margin-bottom: var(--tui-gap-8);

      a {
        @include tui-font-link();
      }
    }
  }
}
</style>
