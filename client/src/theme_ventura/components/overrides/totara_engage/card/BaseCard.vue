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
.tui-baseCard {
  display: flex;
  flex-direction: column;
  width: 100%;

  &__link {
    color: var(--tui-color-neutral-7);
    text-decoration: none;

    &:hover,
    &:focus {
      color: var(--tui-color-neutral-7);
      text-decoration: none;
      outline: none;
    }
  }

  &__card {
    display: flex;
    flex-direction: column;
    width: 100%;
    cursor: default;
  }

  &__headerImage {
    overflow: hidden;
  }

  &__description {
    display: flex;
    flex-direction: column;
    height: 181.31px;
    padding: 0 var(--tui-gap-4) var(--tui-gap-2) var(--tui-gap-4);

    & > :last-child {
      margin-top: auto;
    }

    &__infoContent {
      margin-top: var(--tui-gap-2);
    }
  }
}
</style>
