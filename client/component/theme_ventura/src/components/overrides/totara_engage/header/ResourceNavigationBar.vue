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

  @author Cody Finegan <johannes.cilliers@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-resourceNavigationBar {
  display: flex;
  margin-bottom: var(--tui-gap-12);
  padding: var(--tui-gap-4) var(--tui-gap-8);

  @media (max-width: $tui-screen-sm) {
    padding: var(--tui-gap-4);
  }

  &__backLink {
    flex: 1;
    padding-right: var(--tui-gap-12);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    &:hover {
      text-decoration: none;
    }
  }

  &--disabled {
    color: var(--tui-color-state-disabled);
  }
}
</style>
