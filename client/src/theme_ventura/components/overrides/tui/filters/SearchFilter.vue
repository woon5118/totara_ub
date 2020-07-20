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
.tui-searchFilter {
  position: relative;
  display: flex;
  flex-direction: row;
  align-items: center;

  input.tui-formInput {
    flex-grow: 0;
  }

  .tui-formLabel {
    margin: auto var(--tui-gap-3) auto 0;
  }

  &__group {
    position: relative;
    display: flex;
    flex-grow: 1;
    margin-top: 0;
    margin-bottom: auto;

    &--stacked {
      margin-top: var(--tui-gap-1);
    }

    &-icon {
      position: absolute;
      left: var(--tui-gap-1);
      display: flex;
      align-items: center;
      height: 100%;
      color: var(--tui-filter-search-icon-color);

      .fa-search {
        margin: auto 0;
      }
    }
  }

  &--stacked {
    flex-direction: column;
    align-items: stretch;

    .tui-formLabel {
      margin: var(--tui-gap-1) 0;
    }
  }
}
</style>
