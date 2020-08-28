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

  @author Qingyang Liu <qingyang liu@totaralearning.com>
  @module theme_ventura
-->
<style lang="scss">
.tui-workspaceFileTable {
  @media (max-width: 764px) {
    padding: var(--tui-gap-4);
  }

  &__action {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
  }

  &__loadMoreContainer {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: var(--tui-gap-8);
  }

  &__viewedFiles {
    display: flex;
    align-self: center;
    margin-bottom: var(--tui-gap-1);
  }

  &__loadMore {
    display: flex;
    align-self: center;
  }
}
</style>
