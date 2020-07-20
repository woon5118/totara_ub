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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-dataTableRow {
  position: relative;
  display: flex;
  flex-direction: column;
  padding-top: var(--tui-gap-3);
  padding-bottom: var(--tui-gap-3);
  background: var(--tui-datatable-row-bg-color);
  border-top: 1px solid var(--tui-datatable-row-border-color);

  &:first-child {
    border-top: var(--tui-border-width-normal) solid
      var(--tui-datatable-row-first-border-color);
  }

  &:last-child {
    border-bottom: 1px solid var(--tui-datatable-row-border-color);
  }

  &.tui-focusWithin,
  &:active,
  &:hover {
    background: var(--tui-datatable-row-bg-color-focus);
  }

  &--borderTopFirstOff {
    &:first-child {
      border-top: none;
    }
  }

  &--borderBottomLastOff {
    &:last-child {
      border-bottom: none;
    }
  }

  &--borderSeparatorOff:not(:first-child) {
    border-top: none;
  }

  &--selected {
    background: var(--tui-datatable-row-bg-color-active);

    &:hover {
      background: var(--tui-datatable-row-bg-color-focus);
    }
  }

  &--colorOdd:not(&--selected) {
    &:nth-child(odd) {
      background: var(--tui-datatable-row-bg-color-odd);

      &.tui-focusWithin,
      &:hover {
        background: var(--tui-datatable-row-bg-color-focus);
      }
    }
  }

  &--hoverOff {
    &.tui-focusWithin,
    &:active,
    &:hover {
      background: var(--tui-datatable-row-bg-color);
    }
  }

  &--hoverOff&--colorOdd {
    &:nth-child(odd) {
      &.tui-focusWithin,
      &:hover {
        background: var(--tui-datatable-row-bg-color-odd);
      }
    }
  }

  &--inGroup {
    &:first-child {
      border-top: none;
    }
    &:last-child {
      border-bottom: none;
    }
    &:nth-child(odd) {
      background: none;
    }
    &:hover {
      background: none;
    }
  }

  &--disabled {
    color: var(--tui-color-neutral-6);
  }

  // don't show hover background when another item is being dragged over it
  [data-tui-droppable-any-active] &:hover {
    background: var(--tui-datatable-row-bg-color);
  }

  &--draggable {
    // apply a background so you don't see through the row when dragging
    // (default is transparent)
    background: var(--tui-color-background);
    user-select: none;
    &.tui-focusWithin,
    &:active,
    &:hover {
      background: var(--tui-color-background);
    }
  }

  &--draggable > .tui-dataTableCell {
    pointer-events: none;
  }

  &--dragging {
    box-shadow: var(--tui-shadow-3);
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableRow {
    flex-direction: row;
    padding-right: var(--tui-gap-1);
    padding-left: var(--tui-gap-1);

    & > * + * {
      padding-left: var(--tui-gap-4);
    }

    &--inGroup {
      border-top: none;
      &:last-child {
        border-bottom: none;
      }
    }
  }
}
</style>
