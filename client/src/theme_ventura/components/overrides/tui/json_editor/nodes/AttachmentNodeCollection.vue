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
.tui-attachmentNodeCollection {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: var(--tui-gap-1) 0;
  white-space: normal;
  border-top: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  border-bottom: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  > .tui-attachmentNode {
    // Doing the margin for the single attachment.
    margin-top: var(--tui-gap-1);
    margin-right: var(--tui-gap-1);
    margin-bottom: var(--tui-gap-1);

    @media (min-width: 720px) {
      margin-right: var(--tui-gap-4);
    }
  }
}
</style>
