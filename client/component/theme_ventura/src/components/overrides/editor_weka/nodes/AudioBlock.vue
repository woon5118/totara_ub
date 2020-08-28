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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-editorWeka-audioBlock {
  margin: var(--tui-gap-8) 0;
  white-space: normal;

  &.ProseMirror-selectednode {
    outline: none;
  }

  &.ProseMirror-selectednode > &__inner > .tui-audioBlock {
    outline: var(--tui-border-width-normal) solid var(--tui-weka-select-color);
  }

  &__inner {
    display: inline-block;

    .tui-audioBlock {
      margin: 0;
      white-space: normal;

      audio:focus {
        // Removing self outlininga
        outline: none;
      }
    }
  }
}
</style>
