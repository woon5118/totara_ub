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
.tui-sharedRecipientsSelector {
  &__label.tui-formLabel {
    @include tui-font-heading-label();
    margin-right: 0;
    margin-bottom: var(--tui-gap-2);
  }

  &__icon {
    display: inline-flex;
  }

  &__recipient {
    display: flex;
    > :first-child {
      margin-right: var(--tui-gap-2);
    }

    &__summary {
      margin: 0;
      list-style-type: none;
      > :first-child {
        @include tui-font-heading-label();
      }
      > :last-child {
        @include tui-font-body-small();
      }
    }

    &__badge {
      align-self: flex-end;
      margin-left: auto;
      > :last-child {
        @include tui-font-body-small();
      }
    }
  }

  &__profileContainer {
    position: relative;
    &-badge {
      position: absolute;
      right: 0;
      bottom: 0;
      > :last-child {
        @include tui-font-body-small();
      }
    }
  }
}
</style>
