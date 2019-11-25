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
.tui-editorWeka-linkBlock {
  max-width: 28.6rem;
  margin-bottom: var(--tui-paragraph-gap);
  white-space: normal;

  .tui-dropdown__menu {
    width: auto;
  }

  &__btn:not(:focus) {
    @include sr-only();
  }

  &__btn-wrapper {
    display: flex;
    justify-content: flex-end;
  }
}
</style>
