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
.tui-surveyCard {
  display: flex;
  flex-direction: column;
  width: 100%;

  .tui-card {
    // A hack to set the cursor back to normal cursor instead of pointer.
    cursor: default;
  }

  &__cardContent {
    width: 100%;

    &__calcHeight {
      height: calc(var(--tui-totaraEngage-card-height) - 22px);
    }

    &__height {
      height: var(--tui-totaraEngage-card-height);
    }

    &:hover {
      box-shadow: var(--tui-shadow-2);
    }

    &__inner {
      display: flex;
      flex-direction: column;
      width: 100%;
      height: 100%;
      border: var(--tui-border-width-normal) solid var(--tui-color-primary);
      border-radius: calc(var(--tui-card-border-radius) - 1px);

      &__header {
        display: flex;
        flex-direction: row;
        justify-content: space-between;

        &__image {
          display: flex;
          flex-direction: row;
          padding-top: var(--tui-gap-2);
          padding-left: var(--tui-gap-4);

          img:first-child {
            width: 40px;
            height: 40px;
            margin-top: var(--tui-gap-1);
          }
        }

        &__title {
          @include tui-font-heading-label-small();
          margin-left: var(--tui-gap-2);
          color: var(--tui-color-neutral-6);
          line-height: inherit;
        }

        &__bookmark {
          align-self: flex-start;
          // Negative margin here to neutralise the default redundant edges of icon.
          margin-top: -2px;
        }
      }
    }
  }
}
</style>
