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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-actionLink {
  @extend .tui-formBtn;
  display: inline-block;

  &--prim {
    @extend .tui-formBtn--prim;
  }

  &--small {
    @extend .tui-formBtn--small;
  }

  &--srOnly {
    @extend .tui-formBtn--srOnly;
  }

  &:not([href]) {
    color: var(--tui-btn-text-color-disabled);
    background-color: var(--tui-btn-bg-color-disabled);
    border-color: var(--tui-btn-border-color-disabled);
    cursor: default;
    opacity: 1;
    &:active,
    &:focus,
    &:active:focus,
    &:active:hover,
    &:hover {
      color: var(--tui-btn-text-color-disabled);
      background-color: var(--tui-btn-bg-color-disabled);
      border-color: var(--tui-btn-border-color-disabled);
      box-shadow: none;
    }
  }
}
</style>
