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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-errorDisplay {
  padding: var(--tui-gap-8);
  background: var(--tui-color-neutral-3);
  border-radius: 4px;

  &__title {
    margin-top: 0;
  }

  &__buttons {
    margin-bottom: var(--tui-gap-4);
  }

  &__buttons:last-child {
    margin-bottom: 0;
  }

  &__detailsToggle {
    text-decoration: none;

    &:hover,
    &:focus {
      text-decoration: none;
    }
  }

  &__detailsPre {
    margin-top: var(--tui-gap-2);
    margin-bottom: 0;
    padding: var(--tui-gap-4);
    background-color: var(--tui-color-neutral-1);
    border-radius: 4px;
  }
}
</style>
