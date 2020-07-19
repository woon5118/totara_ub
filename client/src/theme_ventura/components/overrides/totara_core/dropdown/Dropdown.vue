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
  @module theme_ventura
-->

<style lang="scss">
.tui-dropdown {
  position: relative;

  &__menu {
    position: absolute;
    z-index: var(--tui-zindex-dropdown-menu);
    display: none;
    width: 100%;
    min-width: 200px;
    max-height: 50vh;
    overflow-y: auto;
    background-color: var(--tui-dropdown-menu-bg-color);
    background-clip: padding-box;
    border: 1px solid var(--tui-dropdown-menu-border-color);
    box-shadow: var(--tui-shadow-2);
  }

  &__content {
    display: flex;
    flex-direction: column;
    padding: 0.1rem 0.2rem;
    &--separator > * + * {
      border-top: 1px solid var(--tui-dropdown-menu-border-color);
    }
  }

  &--open {
    .tui-dropdown__menu {
      display: block;
    }
  }

  &--disabled {
    color: var(--tui-dropdown-item-text-color-disabled);
    cursor: not-allowed;
    .tui-dropdown__trigger {
      pointer-events: none;
    }
  }

  &--top-right {
    .tui-dropdown__menu {
      top: auto;
      right: 0;
      bottom: 100%;
      left: auto;
    }
  }

  &--top-left {
    .tui-dropdown__menu {
      top: auto;
      right: auto;
      bottom: 100%;
      left: 0;
    }
  }

  &--bottom-left {
    .tui-dropdown__menu {
      right: auto;
      left: 0;
    }
  }

  &--bottom-right {
    .tui-dropdown__menu {
      right: 0;
      left: auto;
    }
  }
}
</style>
