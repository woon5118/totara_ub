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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-competencyDetail {
  &__backLink {
    padding-bottom: var(--tui-gap-2);
  }

  &__body {
    margin-top: var(--tui-gap-6);
    border-top: 1px solid var(--tui-color-neutral-5);

    &-title {
      @include tui-font-heading-small();
    }

    &-empty {
      @include tui-font-hint();
    }
  }

  &__buttons {
    text-align: right;

    .dir-rtl & {
      text-align: left;
    }

    & > * + * {
      margin-top: var(--tui-gap-4);
      margin-left: var(--tui-gap-2);

      .dir-rtl & {
        margin: 0 var(--tui-gap-2) 0 0;
      }
    }
  }

  &__title {
    margin-top: 0;
    @include tui-font-heading-medium();
  }

  &__description {
    @include tui-font-body();
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-competencyDetail {
    &__buttons {
      > * {
        margin-top: 0;
      }
    }
  }
}
</style>
