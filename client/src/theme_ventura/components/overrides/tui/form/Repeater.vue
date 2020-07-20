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
.tui-repeater {
  display: block;
  flex-grow: 1;
  width: 100%;
  min-width: 0;

  & > * {
    margin-bottom: var(--tui-gap-3);
  }

  &--noSpacing > * {
    margin: 0;
  }

  &__row {
    display: flex;
    align-items: flex-start;
  }
}
</style>
