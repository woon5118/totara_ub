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
.tui-formFieldError {
  margin-top: 2px;

  &__inner {
    @include tui-font-body-small();
    display: inline-flex;
    align-items: center;
    padding: 0.6rem 0.8rem;
    color: var(--tui-form-error-text-color);
    background: var(--tui-form-error-bg-color);
  }

  &__dismiss {
    margin-left: var(--tui-gap-1);
    padding: 0;
    color: inherit;
    opacity: 0.7;

    &:hover:active,
    &:active,
    &:hover,
    &:focus {
      color: inherit;
      opacity: 1;
    }

    &-icon {
      position: relative;
      top: -1px;
      @include tui-font-body-small();
    }
  }
}
</style>
