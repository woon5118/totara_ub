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
.tui-criteriaCourseAchievement {
  &__title {
    margin: 0;
    text-align: center;
    hyphens: manual;
    @include tui-font-heading-x-small();
  }

  &__goal {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    max-width: 100%;
  }

  &__progress {
    margin: auto 0;

    &-bar {
      width: 100%;
    }
  }

  &__summary {
    padding: var(--tui-gap-2) var(--tui-gap-2) 0;

    &-header {
      margin: 0;
      @include tui-font-heading-x-small();
    }

    &-body {
      margin-top: var(--tui-gap-4);
    }

    &-button {
      margin-top: var(--tui-gap-4);
    }
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-criteriaCourseAchievement {
    &__completion {
      &-notComplete {
        @include sr-only();
      }
    }
  }
}
</style>
