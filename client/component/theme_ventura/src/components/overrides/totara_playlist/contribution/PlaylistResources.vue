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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-contributionPlaylistResources {
  display: flex;
  width: 100%;
  height: 100%;

  .tui-loader {
    flex-grow: 1;
  }

  &__loadMoreContainer {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  &__viewedResources {
    display: flex;
    align-self: center;
    margin-bottom: var(--tui-gap-1);
  }

  &__loadMore {
    display: flex;
    align-self: center;
  }

  .tui-contributionBaseContent {
    display: flex;
    flex-direction: column;
    width: 100%;

    &__counterContainer {
      margin-bottom: var(--tui-gap-4);

      &__counter {
        position: relative;
        top: 0;
      }
    }
  }
}
</style>
