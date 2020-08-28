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
.tui-commentCard {
  display: flex;
  flex-direction: column;

  &__comment {
    display: flex;
    align-items: flex-start;

    &__profilePicture {
      margin-right: var(--tui-gap-2);
    }

    &__body {
      flex: 1 1 auto;

      &__header {
        margin-bottom: var(--tui-gap-2);
      }

      &__content {
        margin-bottom: var(--tui-gap-2);
        padding-right: var(--tui-gap-2);
        -ms-word-break: break-all;
        overflow-wrap: break-word;
        hyphens: none;
      }
    }
  }
}
</style>
