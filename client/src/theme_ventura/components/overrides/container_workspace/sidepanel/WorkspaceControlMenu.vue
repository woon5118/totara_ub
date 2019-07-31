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
.tui-workspaceControlMenu {
  display: flex;
  width: 100%;

  &__head {
    display: flex;
    flex-direction: column;
    width: 100%;

    &__control {
      display: flex;
      align-items: center;
      padding: var(--tui-gap-4) var(--tui-gap-4) 0 var(--tui-gap-4);

      &__title {
        @include tui-font-heading-small();
        margin: 0;
      }

      &__buttonIcon {
        margin-left: var(--tui-gap-2);
      }
    }

    &__menu {
      padding: var(--tui-gap-8) 0;
      background-color: var(--tui-color-neutral-3);
      border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    }
  }
}
</style>
