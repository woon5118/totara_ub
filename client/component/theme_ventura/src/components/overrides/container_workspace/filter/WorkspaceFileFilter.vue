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
.tui-workspaceFileFilter {
  display: flex;
  flex-direction: column;
  &__sortFilter {
    align-self: flex-end;
    margin-top: var(--tui-gap-8);
    padding-bottom: var(--tui-gap-2);
    .tui-formLabel {
      @include tui-font-heading-label-small();
    }

    @media (max-width: 765px) {
      padding: var(--tui-gap-4);
    }
  }

  &__label {
    .tui-formLabel {
      @include tui-font-heading-label-small();
    }
  }
}
</style>
