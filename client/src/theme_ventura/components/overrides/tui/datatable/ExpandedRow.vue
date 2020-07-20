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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-dataTableExpandableRow {
  position: relative;
  color: var(--tui-datatable-expanded-text-color);
  border: 1px solid var(--tui-datatable-expanded-border-color);
  box-shadow: var(--tui-shadow-2);

  &__close {
    position: absolute;
    top: var(--tui-gap-2);
    right: 0;
    z-index: 2;
    font-size: var(--tui-font-size-20);

    .flex_icon {
      vertical-align: text-top;
    }
  }

  &__content {
    position: relative;
    height: 100vh;
    padding: var(--tui-gap-3);
    overflow-x: hidden;
    overflow-y: scroll;
    background: var(--tui-datatable-expanded-bg-color);
  }

  &__placement {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 0;
    padding: 0;
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableExpandableRow {
    margin: var(--tui-gap-2) 0;

    &__content {
      height: inherit;
      overflow-y: hidden;
    }

    &__placement {
      position: relative;
      top: inherit;
      left: 0;
      z-index: initial;
      width: 100%;
      height: inherit;
    }
  }
}
</style>
