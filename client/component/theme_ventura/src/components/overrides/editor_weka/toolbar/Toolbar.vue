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
.tui-editorWeka-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 0 var(--tui-gap-2);
  border-bottom: 1px solid var(--tui-color-neutral-4);

  &__group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin: 0 var(--tui-gap-2);

    > * + * {
      margin-left: 1px;
    }
  }

  &__currentBlock.tui-iconBtn {
    min-width: 8em;
    padding: 0 var(--tui-gap-1);

    .tui-iconBtn__label {
      flex: 1;
      justify-content: space-between;
    }

    .tui-iconBtn__text {
      padding: 0;
      color: var(--tui-color-text);
    }

    &:disabled .tui-iconBtn__text {
      color: var(--tui-color-state-disabled);
    }
  }
}
</style>
