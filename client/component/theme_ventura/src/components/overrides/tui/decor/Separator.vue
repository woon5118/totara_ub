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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-separator {
  margin: var(--tui-gap-4) 0;

  &--spread {
    margin: var(--tui-gap-8) 0;
  }

  // simple implementation using a horizontal rule element
  &-rule {
    height: var(--tui-border-width-thin);
    line-height: var(--tui-border-width-thin);
    background-color: var(--tui-color-primary);
    border: none;

    &.tui-separator--thick {
      height: var(--tui-border-width-thick);
      line-height: var(--tui-border-width-thick);
    }
  }

  // alternative implementation with wrapper markup to center slotted content
  // horizontal rule visible only to screen readers, the visual lines added with
  // pseudo selectors
  &--wrapped {
    display: flex;
    align-items: center;

    .tui-separator-content {
      margin: 0 var(--tui-gap-4);
      white-space: nowrap;
      text-align: center;
    }

    &:before,
    &:after {
      width: 50%;
      height: var(--tui-border-width-thin);
      line-height: var(--tui-border-width-thin);
      background-color: var(--tui-color-primary);
      content: '';
    }

    &.tui-separator--thick:before,
    &.tui-separator--thick:after {
      height: var(--tui-border-width-thick);
      line-height: var(--tui-border-width-thick);
    }
  }
}
</style>
