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
.tui-dropdownItem {
  @include tui-font-body();
  width: 100%;
  padding: var(--tui-gap-2) var(--tui-gap-4);
  overflow: hidden;
  color: var(--tui-dropdown-item-text-color);
  line-height: 1.4;
  white-space: nowrap;
  text-overflow: ellipsis;
  cursor: pointer;

  &:hover,
  &:focus {
    color: var(--tui-dropdown-item-text-color-hover);
    text-decoration: none;
    background-color: var(--tui-dropdown-item-bg-color-hover);
    outline: 0;
  }

  &:focus {
    color: var(--tui-dropdown-item-text-color-focus);
    background-color: var(--tui-dropdown-item-bg-color-focus);
  }

  &--disabled,
  &--disabled:hover,
  &--disabled:focus {
    color: var(--tui-dropdown-item-text-color-disabled);
    background-color: transparent;
    cursor: not-allowed;
  }

  &--disabled:focus {
    background-color: var(--tui-dropdown-item-bg-color-disabled-focus);
  }

  &--disabled:active {
    pointer-events: none;
  }

  &--noPadding {
    padding: 0;
  }
}
</style>
