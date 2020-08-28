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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-articleRelatedCard {
  display: flex;
  min-width: 120px;
  height: 82px;
  background-color: var(--tui-color-neutral-1);

  &__img {
    width: 80px;
    height: 80px;
    padding: var(--tui-gap-2);
  }

  &__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
    padding: var(--tui-gap-4) 0 var(--tui-gap-2) 0;
    overflow: hidden;

    & > * {
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    > :first-child {
      @include tui-font-heading-label-small();
      @include tui-font-heavy();
      color: inherit;
      text-decoration: none;
    }

    > :last-child {
      display: flex;
      align-items: center;
      margin: 0;
      @include tui-font-body-x-small();
    }
  }

  &__bookmark {
    align-self: flex-start;
    // neutralize the default icon padding
    margin-top: -2px;
  }

  &__timeview {
    display: flex;
    margin-right: var(--tui-gap-4);
    padding: 2px;
    padding-right: var(--tui-gap-1);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: 15px;
  }

  &--dimmed {
    color: var(--tui-color-neutral-6);
  }
}
</style>
