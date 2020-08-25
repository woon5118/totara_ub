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
:root {
  --tui-playlistResources-gridCard-max-height: 347px;
}

.tui-playlistResourcesGrid {
  &__row {
    margin-bottom: var(--tui-gap-5);
  }

  &__card {
    max-height: var(--tui-playlistResources-gridCard-max-height);

    &-item {
      position: relative;
      height: 100%;

      &-moveIcon {
        position: absolute;
        top: var(--tui-gap-2);
        left: var(--tui-gap-2);
        display: none;
      }

      &:hover &-moveIcon,
      &--dragging &-moveIcon {
        z-index: 1;
        display: block;
      }
    }
  }
}
</style>
