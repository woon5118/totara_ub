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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-performManageActivityContent {
  & > * + * {
    margin: var(--tui-gap-4) 0 0;
  }

  &__items {
    & > * + * {
      margin-top: var(--tui-gap-4);
    }
  }

  &__heading {
    @include tui-font-heading-small();
  }
}
</style>
