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
.tui-sidePanelNavLinkItem {
  display: flex;
  align-items: center;
  color: var(--tui-side-panel-nav-item-text-color);
  background: var(--tui-side-panel-nav-item-bg-color);
  border-color: var(--tui-side-panel-nav-item-border-color);

  // Item li
  &.tui-focusWithin,
  &:hover {
    color: var(--tui-side-panel-nav-item-text-color-focus);
    background: var(--tui-side-panel-nav-item-bg-color-focus);
    border-color: var(--tui-side-panel-nav-item-border-color-focus);
  }

  &__side {
    margin-left: auto;
    padding-right: var(--tui-gap-4);
  }

  // Item link
  &__action {
    flex-grow: 1;
    padding: var(--tui-gap-2) var(--tui-gap-4);
    color: inherit;
    line-height: 1;
    -ms-word-break: break-all;
    word-break: break-word;

    &:hover,
    &:focus,
    &:focus:hover {
      color: inherit;
    }
  }

  &--active {
    color: var(--tui-side-panel-nav-item-text-color-selected);
    background: var(--tui-side-panel-nav-item-bg-color-selected);
    border-color: var(--tui-side-panel-nav-item-border-color-selected);

    &.tui-focusWithin,
    &:hover {
      color: var(--tui-side-panel-nav-item-text-color-selected);
      background: var(--tui-side-panel-nav-item-bg-color-selected);
      border-color: var(--tui-side-panel-nav-item-border-color-selected);
    }
  }
}
</style>
