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
.tui-engageArticle-articleCard {
  &__imageheader {
    padding: var(--tui-gap-4) var(--tui-gap-4) 0 var(--tui-gap-4);
  }

  &__image {
    display: block;
    width: 100%;
    height: 100%;
  }

  &__icons {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    & > * + * {
      margin-left: var(--tui-gap-4);
    }
  }

  &__bookmark {
    // Negative margin here to neutralise the default redundant edges of icon.
    margin-top: -2px;
    margin-right: calc(var(--tui-gap-3) * -1);
  }

  &__title {
    @include tui-font-heading-x-small();
    overflow-wrap: break-word;
  }

  &__subTitle {
    display: inline-flex;
    align-items: center;
    margin-top: var(--tui-gap-2);
    padding-right: var(--tui-gap-1);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: 50px;
  }

  &__footer {
    display: flex;
    align-items: flex-end;

    & > * + * {
      margin-left: var(--tui-gap-1);
    }

    & > :last-child {
      margin: 0 -5px 0 auto;
    }
  }
}
</style>
