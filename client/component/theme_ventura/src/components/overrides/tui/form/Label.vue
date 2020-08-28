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
.tui-formLabel {
  @include tui-font-heading-label();
  min-width: 0;
  margin: 0;
  padding: 0 var(--tui-gap-1) 0 0;

  legend& {
    width: auto;
    margin: 0;
    padding: 0 var(--tui-gap-1) 0 0;
    color: inherit;
    border: none;
  }

  &--inline {
    display: inline;
  }

  &--hidden {
    @include sr-only();
  }

  &--subfield {
    @include tui-font-body();
  }

  &__required {
    color: var(--tui-color-prompt-alert);
  }
}
</style>
