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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @module theme_ventura
-->
<style lang="scss">
.tui-performActivitySectionRelationship {
  > * {
    margin-top: var(--tui-gap-4);
  }
  &__item {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    &-name {
      display: flex;
      flex-grow: 1;
      justify-content: space-between;
      padding: var(--tui-gap-2);
      border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
      border-radius: var(--tui-border-radius-normal);
    }
    &-options {
      margin-top: var(--tui-gap-4);
    }
  }
}
@media (min-width: $tui-screen-xs) {
  .tui-performActivitySectionRelationship {
    > * {
      margin-top: var(--tui-gap-2);
    }
    &__item {
      flex-direction: row;
      &-name {
        max-width: 500px;
      }
      &-options {
        margin: auto 0;
        padding: var(--tui-gap-2);
      }
    }
  }
}
</style>
