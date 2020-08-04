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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-miniProfileCard {
  // The parent who uses this card decides the width/height.
  display: flex;
  align-items: flex-start;
  padding: var(--tui-gap-2);
  outline: none;

  &--no-avatar {
    padding-left: var(--tui-gap-4);
  }

  &--no-dropDown {
    padding-right: var(--tui-gap-4);
  }

  &--no-padding {
    // Reset padding to zero.
    padding: 0;
  }

  &--border {
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: var(--tui-border-radius-normal);
  }

  &--hasShadow {
    box-shadow: var(--tui-shadow-2);
  }

  &__avatar {
    margin-right: var(--tui-gap-2);
    &--readOnly:hover,
    &--readOnly:focus {
      text-decoration: none;
    }
  }

  &__description {
    display: flex;
    flex: 1;
    flex-direction: column;
    overflow: hidden;

    &__text {
      @include tui-font-body-small();
      margin: 0;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;

      &--position-zero {
        @include tui-font-heavy();
      }

      &--with-gap {
        margin-bottom: var(--tui-gap-1);
      }
    }

    &__link {
      @include tui-font-link-small();
      margin: 0;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;

      &--position-zero {
        @include tui-font-heavy();
      }

      &--with-gap {
        margin-bottom: var(--tui-gap-1);
      }

      &--readOnly:hover,
      &--readOnly:focus {
        text-decoration: none;
      }
    }
  }

  &__dropDown {
    margin-left: var(--tui-gap-4);
  }
}
</style>
