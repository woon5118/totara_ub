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
.tui-editorWeka-linkMedia {
  display: flex;
  flex-direction: column;
  margin-bottom: var(--tui-paragraph-gap);
  white-space: normal;

  &--intrinsic-width {
    align-items: flex-start;
  }

  &.ProseMirror-selectednode {
    outline: none;
  }
  &.ProseMirror-selectednode > &__inner {
    outline: var(--tui-border-width-normal) solid var(--tui-weka-select-color);
  }

  &__inner {
    max-width: 700px;

    & > .tui-editorWeka-nodeBar {
      margin-top: var(--tui-gap-2);
    }
  }
}
</style>
