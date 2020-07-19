<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-adder {
  margin-top: calc(var(--tui-gap-6) - var(--tui-modal-content-separation));

  &__list {
    height: 316px;
    overflow-y: auto;

    &-loadMore {
      margin: var(--tui-gap-1) 0 0;
      text-align: center;
    }

    &::before {
      display: block;
      height: var(--tui-gap-4);
      content: '';
    }
  }

  &__footer {
    display: flex;
    padding-top: var(--tui-gap-6);
    border-top: var(--tui-border-width-normal) solid var(--tui-color-neutral-5);
  }

  &__summary {
    display: flex;
    align-items: center;
    width: 50%;
    @include tui-font-heading-label();
  }

  &__actions {
    display: flex;
    justify-content: flex-end;
    width: 50%;
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-adder {
    .tui-tabContent {
      min-height: 380px;
    }

    &__listBasket {
      min-height: 350px;
    }
  }
}
</style>
