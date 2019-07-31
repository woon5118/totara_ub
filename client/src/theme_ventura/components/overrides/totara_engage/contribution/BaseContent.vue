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
.tui-contributionBaseContent {
  &__horizontal {
    padding: var(--tui-gap-8);
  }

  &__vertical {
    .tui-filterBar__filters,
    .tui-contributionBaseContent__cards {
      padding: var(--tui-gap-4);
    }

    .tui-contributionBaseContent__counterContainer {
      padding: 0 var(--tui-gap-2);
    }
    .tui-contributionFilter__sort {
      padding-right: var(--tui-gap-4);
    }
  }

  &__header {
    display: flex;
    justify-content: space-between;
    margin-top: var(--tui-gap-8);
    margin-bottom: calc(var(--tui-gap-8) * 2);

    &__title {
      @include tui-font-heading-large;
      flex: 1;
    }

    :not(:first-child) {
      margin-left: var(--tui-gap-8);
    }
  }

  &__filter {
    display: flex;
    flex-direction: column;
    margin-top: var(--tui-gap-4);
    margin-bottom: var(--tui-gap-4);
  }

  &__cards {
    margin-top: var(--tui-gap-1);
    padding: var(--tui-gap-1);
    & > * + * {
      margin-top: var(--tui-gap-2);
    }
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

  &__counterContainer {
    position: relative;

    &__counter {
      @include tui-font-heading-x-small;
      position: absolute;
      top: calc(var(--tui-gap-6) * -2);
      padding: var(--tui-gap-2);
      padding-bottom: 0;
    }
  }

  &__emptyText {
    @include tui-font-body;
    margin-top: var(--tui-gap-2);
  }
}
</style>
