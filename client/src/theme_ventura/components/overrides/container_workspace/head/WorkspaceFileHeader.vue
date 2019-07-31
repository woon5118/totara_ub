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
.tui-workspaceFileHeader {
  display: flex;
  flex-direction: column;

  @media (max-width: 764px) {
    padding: var(--tui-gap-4);
  }

  &__back {
    .tui-iconBtn {
      padding: 0;
    }
  }

  &__title {
    @include tui-font-heading-large();
    margin-top: var(--tui-gap-10);
    margin-bottom: var(--tui-gap-12);
  }
}
</style>
