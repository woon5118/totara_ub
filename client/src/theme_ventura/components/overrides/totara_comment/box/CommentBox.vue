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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-commentBox {
  display: flex;
  flex-direction: column;
  height: 100%;

  // The parent that using this box should be able to override its padding.
  padding: 0;

  &--withBorder {
    background-color: var(--tui-color-neutral-3);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

    &.tui-commentBox--noForm {
      // When the form is not available for the comment box, it will leave us a trailing border. Therefore, this
      // modifier is in place in order to prevent that traing border.
      border-bottom: none;
    }
  }

  &__commentForm {
    flex-basis: 20%;
    margin-top: var(--tui-gap-4);

    &--withBorder {
      padding: var(--tui-gap-4);
      padding-top: 0;
    }
  }
}
</style>
