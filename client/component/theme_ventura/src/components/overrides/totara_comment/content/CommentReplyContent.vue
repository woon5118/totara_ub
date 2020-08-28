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
.tui-commentReplyContent {
  margin: 0;

  &__content {
    max-width: 100%;

    .tui-rendered {
      p {
        @include tui-font-body();
        // Reset margin of paragraph in tui-rendered content.
        margin: 0;
      }
    }

    &--deleted {
      // Deleted comment should not have any .tui-rendered element
      @include tui-font-body-small();
      font-style: italic;
    }

    &--reply {
      @include tui-font-body-small();

      .tui-rendered {
        p {
          @include tui-font-body-small();
        }
      }
    }
  }
}
</style>
