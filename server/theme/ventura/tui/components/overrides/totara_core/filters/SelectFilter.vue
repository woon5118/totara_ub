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
.tui-selectFilter {
  position: relative;
  display: flex;
  flex-direction: row;
  align-items: center;

  .tui-formLabel {
    margin: auto var(--tui-gap-3) auto 0;
  }

  .tui-select {
    max-width: 250px;
  }

  &--stacked {
    flex-direction: column;
    align-items: stretch;

    .tui-formLabel {
      margin: var(--tui-gap-1) 0 0;
    }

    .tui-select {
      max-width: initial;
      margin-top: var(--tui-gap-1);
    }
  }
}
</style>
