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
.tui-confirmDeleteCommentReplyModal {
  &__buttons {
    display: flex;
    justify-content: flex-end;
  }

  &__textBox {
    display: flex;
    align-items: center;
    justify-content: flex-start;

    &__text {
      @include tui-font-body();
      margin: 0;
      margin-left: var(--tui-gap-4);
    }
  }

  &__content {
    .tui-modalContent {
      &__title {
        padding: 0;
      }
    }
  }
}
</style>
