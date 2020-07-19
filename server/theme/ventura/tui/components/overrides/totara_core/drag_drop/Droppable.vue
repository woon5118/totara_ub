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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<style lang="scss">
.tui-droppable {
  &__dropLine {
    z-index: var(--tui-zindex-draggable);
    background: var(--tui-color-secondary);
    pointer-events: none;

    &--vertical {
      height: var(--tui-border-width-normal);
      margin-top: calc(var(--tui-border-width-normal) * -0.5);
    }

    &--horizontal {
      width: var(--tui-border-width-normal);
      margin-left: calc(var(--tui-border-width-normal) * -0.5);
    }
  }
}
[data-tui-droppable-location-indicator] {
  background-color: var(--tui-color-neutral-5);
}
</style>
