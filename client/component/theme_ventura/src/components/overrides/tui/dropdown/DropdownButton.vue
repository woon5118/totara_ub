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
  &:hover,
  &:focus,
  &:active,
  &:focus:active {
    border-color: transparent;
    box-shadow: none;
  }
}

// tui-dropdownButton styles
.tui-dropdownButton {
  @include tui-font-body();
  padding: var(--tui-gap-2) var(--tui-gap-4);
  overflow: hidden;
  line-height: 1.4;
  text-overflow: ellipsis;

  &:hover,
  &:focus,
  &:active,
  &:focus:active {
    color: var(--tui-dropdown-item-text-color-hover);
    text-decoration: none;
    background-color: var(--tui-dropdown-item-bg-color-hover);
    outline: 0;
  }

  &:focus,
  &:active,
  &:focus:active {
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

  &--disabled:active {
    pointer-events: none;
  }

  &--noPadding {
    padding: 0;
  }
}
</style>
