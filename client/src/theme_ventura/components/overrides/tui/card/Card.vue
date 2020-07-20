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
:root {
  --tui-card-border-radius: var(--tui-border-radius-normal);
}

.tui-card {
  display: flex;
  border: 1px solid var(--tui-card-border-color);
  border-radius: var(--tui-card-border-radius);
  outline: none;

  &--noBorder {
    border: none;
    &:focus,
    &:hover {
      border: none;
    }
  }

  &--hasShadow {
    box-shadow: var(--tui-shadow-2);
  }

  &--clickable {
    transition: box-shadow var(--tui-transition-form-function)
      var(--tui-transition-form-duration);
  }

  &--clickable:hover,
  &--clickable:focus {
    box-shadow: var(--tui-shadow-2);
    cursor: pointer;
  }

  &.tui-focusWithin {
    box-shadow: var(--tui-shadow-2);
  }
}
</style>
