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
.tui-tag {
  display: inline-flex;
  flex-shrink: 0;
  align-items: center;
  height: 24px;
  white-space: nowrap;
  background-color: var(--tui-tag-bg-color);
  border: 1px solid var(--tui-tag-border-color);
  border-radius: 12px;

  &__content {
    padding: 0 var(--tui-gap-2);
    color: var(--tui-tag-active-color);
    font-size: 13px;

    &--withButton {
      padding-right: 0;
      color: var(--tui-tag-button-color);
    }
  }

  &--primary {
    text-transform: uppercase;
    background-color: var(--tui-color-state);
  }

  &--primary &__content {
    color: var(--tui-color-neutral-1);
  }
}
</style>
