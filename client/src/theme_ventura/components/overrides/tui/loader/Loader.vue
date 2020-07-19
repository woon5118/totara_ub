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
.tui-loader {
  $loading-fullpage: #{&}--fullpage;

  position: relative;

  // Don't show nested loaders
  &--active > * .tui-loader__overlay {
    display: none;
  }

  // If no slot content (e.g. initial load)
  &__empty {
    min-height: var(--tui-gap-8);
  }

  &__overlay {
    position: absolute;
    top: 0;
    /*rtl:ignore*/
    left: 0;
    width: 100%;
    height: 100%;

    #{$loading-fullpage} & {
      position: fixed;
      z-index: var(--tui-zindex-loading-page);
    }

    &-positioner {
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      background: var(--tui-color-background);
      opacity: 0.85;
      content: '';
    }
  }

  &__display {
    position: sticky;
    top: var(--tui-gap-6);
    bottom: var(--tui-gap-6);
    font-size: var(--tui-font-size-18);

    .fa-spinner {
      position: relative;
      top: -1px;
    }
  }
}
</style>
