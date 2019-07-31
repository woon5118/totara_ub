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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-contributeModal {
  position: relative;

  &__adderContainer {
    margin-bottom: var(--tui-gap-2);
    padding: 0 var(--tui-gap-8);
    &__title {
      @include tui-font-heading-small();
      margin-top: 0;
    }
  }

  &__resize.tui-iconBtn {
    position: absolute;
    top: var(--tui-gap-5);
    right: var(--tui-gap-5);
  }

  &__tabs {
    display: flex;
    flex-direction: column;
    padding: 0;

    .tui-tabs {
      &__tabs {
        padding-right: var(--tui-gap-8);
        padding-left: var(--tui-gap-8);
      }

      &__panels {
        display: flex;
        flex-direction: column;
      }
    }
  }

  &__componentContent {
    position: relative;
    display: flex;
    flex: 1;
    flex-direction: column;
    width: 100%;
    height: 100%;
    min-height: 510px;
    padding: 0 var(--tui-gap-8);
  }

  .tui-modalContent__title {
    display: none;
  }

  .tui-modalContent__content {
    display: flex;
    flex-direction: column;
    min-height: 510px;
    margin-top: 0;
    padding: 0;
  }

  .tui-tabContent {
    position: relative;
    flex: 1;
    min-height: 400px;
    padding: var(--tui-gap-8) 0;
  }

  .tui-modalContent__header-title {
    margin-bottom: var(--tui-gap-2);
  }
}
</style>
