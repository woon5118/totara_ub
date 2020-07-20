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
.tui-basket {
  @include tui-font-body();

  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  background-color: var(--tui-color-neutral-3);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  @media (min-width: $tui-screen-sm) {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }

  &__status,
  &__actions {
    display: flex;
    flex-shrink: 0;
    flex-wrap: wrap;
    align-items: center;
    padding: var(--tui-gap-2) var(--tui-gap-3);

    > .tui-formBtn--transparent {
      padding: var(--tui-gap-1);
    }

    > * + * {
      margin-left: var(--tui-gap-2);
    }
  }

  &__selected {
    font-weight: bold;
  }

  &__selectedCount {
    font-size: var(--tui-font-size-18);
  }
}
</style>
