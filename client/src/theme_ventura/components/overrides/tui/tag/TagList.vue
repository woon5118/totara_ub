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
.tui-tagList {
  display: flex;
  align-items: flex-start;
  min-width: 230px;
  padding: var(--tui-gap-2);
  border: var(--tui-border-width-thin) solid var(--tui-form-input-border-color);

  &__tags {
    display: flex;
    align-items: center;
    width: 100%;
    min-width: 0;
  }

  &__tagItems {
    display: flex;
    align-items: center;
    min-width: 0;

    &--open {
      flex-wrap: wrap;
    }

    & > * {
      margin-right: var(--tui-gap-1);
      margin-bottom: 0.2rem;
    }
  }

  &__suffix {
    @include tui-font-body-small();
    flex-shrink: 0;
    padding-right: var(--tui-gap-2);
    padding-left: var(--tui-gap-1);
    white-space: nowrap;
  }

  &__input {
    margin: -2px 0;
  }
}
</style>
