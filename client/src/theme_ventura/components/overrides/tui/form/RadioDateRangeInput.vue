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
.tui-radioDateRangeInput {
  display: flex;
  // Use same indentation as radio for nicer wrapping
  margin-left: var(--tui-radio-label-offset);

  & > * + * {
    margin-left: var(--tui-gap-2);
  }

  &__number {
    display: flex;
    width: 6rem;
  }

  &__type {
    display: flex;
    width: 12rem;
  }
}
</style>
