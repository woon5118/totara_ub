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
.tui-competencyUserHeader {
  position: relative;
  display: flex;
  align-items: center;

  &::after {
    position: absolute;
    right: 0;
    bottom: 0;
    left: 19px; // half of xsmall avatar (which is specified in px)
    border-bottom: var(--tui-border-width-normal) solid var(--tui-color-primary);
    content: '';
  }

  &__name {
    @include tui-font-body();
    // offset name by the border width so it doesn't look weird
    margin-bottom: var(--tui-border-width-normal);
    margin-left: var(--tui-gap-2);
    font-weight: bold;
  }
}
</style>
