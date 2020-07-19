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
.tui-searchBox {
  display: flex;

  .tui-formLabel {
    margin-right: var(--tui-gap-2);
  }

  // So that the search button matches the format of the input that is next to it
  &__button {
    border-color: var(--tui-form-input-border-color);
    border-style: solid;
    border-width: var(--tui-form-input-border-size)
      var(--tui-form-input-border-size) var(--tui-form-input-border-size) 0;

    &:hover,
    &:active,
    &:focus {
      background-color: var(--tui-btn-bg-color-focus);
      border-color: var(--tui-form-input-border-color);
      border-style: solid;
      border-width: var(--tui-form-input-border-size)
        var(--tui-form-input-border-size) var(--tui-form-input-border-size) 0;
      box-shadow: var(--tui-btn-shadow-focus);
    }
  }
}
</style>
