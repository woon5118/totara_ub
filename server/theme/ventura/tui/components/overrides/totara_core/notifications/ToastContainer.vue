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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-toastContainer {
  position: fixed;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: var(--tui-zindex-toast);
  display: flex;
  justify-content: center;
  pointer-events: none;

  &--hasItems &__inner {
    padding-bottom: var(--tui-gap-3);
  }

  &__inner {
    position: relative;
    flex-basis: 700px;
    pointer-events: auto;

    & .notificationBanner + .notificationBanner {
      margin-top: var(--tui-gap-4);
    }
  }

  &__list-item {
    margin: var(--tui-gap-3);
    margin-top: 0;
    transition: all 0.15s ease-out;
  }

  &__list-transition {
    &-enter {
      transform: translateY(calc(var(--tui-gap-12) + var(--tui-gap-3)));
      opacity: 0;
    }

    &-leave {
      transform: none;
    }

    &-leave-to {
      transform: none;
      opacity: 0;
    }

    &-leave-active {
      position: absolute;
      right: 0;
      left: 0;
      transform: none;
      opacity: 0;
    }
  }
}
</style>
