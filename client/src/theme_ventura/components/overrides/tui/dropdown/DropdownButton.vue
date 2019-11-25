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

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
// override <button> styles
.tui-dropdownButton {
  color: var(--tui-dropdown-item-text-color);
  text-align: left;
  border: none;
  border-radius: 0;
  &:hover {
    border-color: transparent;
    box-shadow: none;
  }
}

// tui-dropdownButton styles
.tui-dropdownButton {
  margin: 0.1rem 0;
  padding: var(--tui-gap-2) var(--tui-gap-4);
  overflow: hidden;
  font-size: var(--tui-font-size-16);
  line-height: 1.4;
  text-overflow: ellipsis;

  &:hover {
    color: var(--tui-dropdown-item-text-color-focus);
    background-color: var(--tui-dropdown-item-bg-color-focus);
  }

  &:focus {
    @include tui-focus();
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
}
</style>
