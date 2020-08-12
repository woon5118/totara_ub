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
.tui-inlineEditing {
  position: relative;
  display: inline-flex;
  align-items: flex-start;
  justify-content: space-between;
  margin: calc(-1 * var(--tui-gap-1));
  padding: var(--tui-gap-1);
  border: 2px solid transparent;

  &--fullWidth {
    width: 100%;
  }

  &--updateAble {
    &:hover,
    &:focus-within,
    &.tui-focusWithin {
      border: 2px solid var(--tui-color-secondary);
      cursor: pointer;
      & .tui-inlineEditing__btn {
        outline: none;
      }
    }
  }

  &:not(:hover) {
    .tui-inlineEditing__btn:not(:focus) {
      visibility: hidden;
    }
  }
}
</style>
