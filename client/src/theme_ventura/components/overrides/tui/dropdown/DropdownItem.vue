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
  width: 100%;
  margin: 0.1rem 0;
  padding: var(--tui-gap-2) var(--tui-gap-4);
  overflow: hidden;
  color: var(--tui-dropdown-item-text-color);
  font-size: var(--tui-font-size-16);
  line-height: 1.4;
  white-space: nowrap;
  text-overflow: ellipsis;
  cursor: pointer;

  &:hover {
    color: var(--tui-dropdown-item-text-color-focus);
    text-decoration: none;
    background-color: var(--tui-dropdown-item-bg-color-focus);
    outline: 0;
  }

  &:focus {
    @include tui-focus();
    text-decoration: none;
    background-color: var(--tui-dropdown-item-bg-color-focus);
  }

  &--disabled,
  &--disabled:hover,
  &--disabled:focus {
    color: var(--tui-btn-text-color-disabled);
    background-color: var(--tui-btn-bg-color-disabled);
    cursor: not-allowed;
  }

  &--disabled:active {
    pointer-events: none;
  }

  &--small {
    padding: var(--tui-gap-1) var(--tui-gap-2);
    font-size: var(--tui-font-size-14);
  }

  &--paddingless {
    padding: 0;
  }
}
</style>
