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
  @module tui
-->

<template>
  <div
    v-focus-within="!inGroup"
    class="tui-dataTableRow"
    :class="{
      'tui-dataTableRow--colorOdd': colorOdd,
      'tui-dataTableRow--disabled': disabled,
      'tui-dataTableRow--inGroup': inGroup,
      'tui-dataTableRow--selected': selected,
      'tui-dataTableRow--draggable': draggable,
      'tui-dataTableRow--dragging': dragging,
      'tui-dataTableRow--borderTopFirstOff': borderTopHidden,
      'tui-dataTableRow--borderBottomLastOff': borderBottomHidden,
      'tui-dataTableRow--borderSeparatorOff': borderSeparatorHidden,
      'tui-dataTableRow--hoverOff': hoverOff,
      'tui-dataTableRow--expanded': expanded,
    }"
    role="row"
  >
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    borderBottomHidden: Boolean,
    borderSeparatorHidden: Boolean,
    borderTopHidden: Boolean,
    colorOdd: Boolean,
    disabled: Boolean,
    hoverOff: Boolean,
    inGroup: Boolean,
    selected: Boolean,
    draggable: Boolean,
    dragging: Boolean,
    expanded: Boolean,
  },
};
</script>

<style lang="scss">
.tui-dataTableRow {
  position: relative;
  display: flex;
  flex-direction: column;
  padding-top: var(--gap-3);
  padding-bottom: var(--gap-3);
  background: var(--datatable-row-bg-color);
  border-top: 1px solid var(--datatable-row-border-color);

  &:first-child {
    border-top: var(--border-width-normal) solid
      var(--datatable-row-first-border-color);
  }

  &:last-child {
    border-bottom: 1px solid var(--datatable-row-border-color);
  }

  &.tui-focusWithin,
  &:active,
  &:hover {
    background: var(--datatable-row-bg-color-focus);
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
    background: var(--datatable-row-bg-color-active);

    &:hover {
      background: var(--datatable-row-bg-color-focus);
    }
  }

  &--colorOdd:not(&--selected) {
    &:nth-child(odd) {
      background: var(--datatable-row-bg-color-odd);

      &.tui-focusWithin,
      &:hover {
        background: var(--datatable-row-bg-color-focus);
      }
    }
  }

  &--hoverOff {
    &.tui-focusWithin,
    &:active,
    &:hover {
      background: var(--datatable-row-bg-color);
    }
  }

  &--hoverOff&--colorOdd {
    &:nth-child(odd) {
      &.tui-focusWithin,
      &:hover {
        background: var(--datatable-row-bg-color-odd);
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
    color: var(--color-neutral-6);
  }

  // don't show hover background when another item is being dragged over it
  [data-tui-droppable-any-active] &:hover {
    background: var(--datatable-row-bg-color);
  }

  &--draggable {
    // apply a background so you don't see through the row when dragging
    // (default is transparent)
    background: var(--color-background);
    user-select: none;
    &.tui-focusWithin,
    &:active,
    &:hover {
      background: var(--color-background);
    }
  }

  &--draggable > .tui-dataTableCell {
    pointer-events: none;
  }

  &--dragging {
    box-shadow: var(--shadow-3);
  }

  &--expanded {
    margin-left: calc(0px - var(--border-width-thin));
    background-color: var(--datatable-expanded-bg-color);
    border: var(--border-width-thin) solid
      var(--datatable-expanded-border-color);
    border-bottom: none;
    box-shadow: var(--shadow-2);
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableRow {
    flex-direction: row;
    padding-right: var(--gap-1);
    padding-left: var(--gap-1);

    & > * + * {
      padding-left: var(--gap-4);
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
