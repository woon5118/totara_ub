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
.tui-competencyDetailActivityLog {
  margin: var(--tui-gap-2) var(--tui-gap-4);

  &__title {
    @include tui-font-heading-small();
    padding-top: var(--tui-gap-2);
  }

  &__date {
    word-wrap: break-word;
  }

  &__description {
    .flex-icon {
      position: relative;
      top: -1px;
    }

    &-rating {
      font-weight: bold;
    }

    &-tracking {
      font-weight: bold;
      text-transform: uppercase;
    }

    &-system {
      &::before {
        content: '* ';
      }
      &::after {
        content: ' *';
      }
    }
  }
}

@media screen and (min-width: $tui-screen-xs) {
  .tui-competencyDetailActivityLog {
    &__proficient {
      font-weight: bold;
      &-srOnly {
        @include sr-only();
      }
    }
  }
}
</style>
