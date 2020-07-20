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
.tui-formHelpIcon {
  @include tui-font-heading-label();
  display: inline-block;

  &__icon {
    color: var(--tui-color-state);
  }

  &__desc {
    // aria-describedby still works even if the target element is display: none
    // https://developer.paciellogroup.com/blog/2015/05/short-note-on-aria-labelledby-and-aria-describedby/
    display: none;
  }
}
</style>
