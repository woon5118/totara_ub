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
.tui-playlistStarRating {
  &:after {
    display: flex;
    margin-top: calc(var(--tui-gap-10) * 4);
    border-bottom: 1px solid var(--tui-color-neutral-4);
    content: '';
  }

  .tui-totaraEngage-star {
    width: var(--tui-font-size-15);
    height: var(--tui-font-size-15);

    &__filled {
      stop-color: var(--tui-color-chart-background-2);
    }

    &__unfilled {
      stop-color: var(--tui-color-neutral-1);
    }
  }

  &__rates {
    padding-right: var(--tui-gap-1);
  }

  &__icon {
    margin-bottom: var(--tui-gap-1);
  }
}
</style>
