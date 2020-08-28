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
.tui-wekaWithLearn {
  display: flex;
  flex-direction: column;

  &__title {
    @include tui-font-heading-small();
    margin-bottom: var(--tui-gap-8);
  }

  &__form {
    display: flex;
    flex: 1;
    flex-direction: column;

    &__row {
      flex: 1;
    }

    &__buttonGroup {
      display: flex;
      justify-content: flex-end;
      margin-top: var(--tui-gap-2);
    }
  }

  &__code {
    display: flex;
    padding-left: var(--tui-gap-2);
  }
}
</style>
