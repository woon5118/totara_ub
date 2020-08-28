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
.tui-workspaceMemberRequestList {
  padding: var(--tui-gap-2);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  &__title {
    @include tui-font-heading-x-small();
    margin: 0;
    padding-bottom: var(--tui-gap-2);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  }

  &__cards {
    display: flex;
    flex-direction: column;

    &__card {
      padding: var(--tui-gap-2) 0;
      &:not(:last-child) {
        border-bottom: var(--tui-border-width-thin) solid
          var(--tui-color-neutral-5);
      }
    }
  }
}
</style>
