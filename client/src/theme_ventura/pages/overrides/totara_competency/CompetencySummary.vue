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
.tui-competencySummary {
  @include tui-font-body();

  & > * + * {
    margin-top: var(--tui-gap-4);
  }

  &__header {
    &-title {
      @include tui-font-heading-medium();
      margin: var(--tui-gap-2) 0 0;
    }
  }

  &__sectionHeader {
    display: flex;
    align-items: center;
    padding-bottom: var(--tui-gap-1);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

    &-title {
      margin: 0;
      @include tui-font-heading-small();
    }

    &-edit {
      margin-left: auto;
      padding-left: var(--tui-gap-2);
    }
  }
}
</style>
