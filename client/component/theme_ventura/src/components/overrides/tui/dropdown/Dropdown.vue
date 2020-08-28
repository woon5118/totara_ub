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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-dropdown {
  &__menu {
    z-index: var(--tui-zindex-dropdown-menu);
    min-width: 15rem;
    max-width: 35rem;
    max-height: 40vh;
    overflow-y: auto;
    background-color: var(--tui-dropdown-menu-bg-color);
    background-clip: padding-box;
    border: 1px solid var(--tui-dropdown-menu-border-color);
    box-shadow: var(--tui-shadow-2);
  }

  &__content {
    display: flex;
    flex-direction: column;
    &--separator > * + * {
      border-top: 1px solid var(--tui-dropdown-menu-border-color);
    }
  }

  &--disabled {
    cursor: not-allowed;
    .tui-dropdown__trigger {
      pointer-events: none;
    }
  }
}
</style>
