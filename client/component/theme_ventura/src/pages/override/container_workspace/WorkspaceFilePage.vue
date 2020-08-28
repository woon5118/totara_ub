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
.tui-workspaceFilePage {
  display: flex;
  flex-direction: column;

  // Overriding the css here.
  .tui-responsive {
    flex: 1;
    width: 100%;
    height: 100%;

    .tui-grid--vertical {
      display: flex;
      flex-direction: column-reverse;

      .tui-grid-item {
        border-top: 0;
      }
    }

    .tui-layoutTwoColumn__heading {
      margin: 0;
    }
  }

  &__content {
    @media (min-width: $tui-screen-sm) {
      padding: var(--tui-gap-8);
    }

    &__message {
      @include tui-font-body();
      margin: var(--tui-gap-8) 0;
    }
  }
}
</style>
