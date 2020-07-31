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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-toastContainer {
  position: fixed;
  top: var(--tui-gap-6);
  right: 0;
  left: 0;
  z-index: var(--tui-zindex-toast);
  display: flex;
  justify-content: center;
  pointer-events: none;

  &__inner {
    position: relative;
    flex-basis: 300px;
    pointer-events: auto;
  }

  &__item-transition {
    &-enter-active,
    &-leave-active {
      transition: all 0.15s ease-out;
    }

    &-enter,
    &-leave-to {
      opacity: 0;
    }

    &-leave-active {
      position: absolute;
      right: 0;
      left: 0;
      opacity: 0;
    }
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-toastContainer {
    &__inner {
      flex-basis: 500px;
    }
  }
}
</style>
