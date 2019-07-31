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
.tui-summaryPlaylistCard {
  display: flex;
  flex-direction: column;
  align-items: flex-start;

  &__top {
    display: flex;
    width: 100%;

    &__title {
      @include tui-font-link-small();
      @include tui-font-heavy();
      flex: 1;
      margin: 0;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    // Overriding the star rating
    .tui-totaraEngage-star {
      width: var(--tui-font-size-15);
      height: var(--tui-font-size-14);

      &__filled {
        stop-color: var(--tui-color-chart-background-2);
      }

      &__unfilled {
        stop-color: var(--tui-color-neutral-1);
      }
    }
  }

  &__author {
    @include tui-font-body-x-small();
    margin: 0;
    margin-top: var(--tui-gap-1);
  }
}
</style>
