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
.tui-formRow {
  display: flex;
  flex-direction: column;

  // __inner is needed as setting margin on the root element is the
  // responsibility of the containing element (outside-in spacing).
  // (see below for margin setting)
  &__inner {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    flex-wrap: wrap;
  }

  &__inner > &__desc {
    min-width: 0;
    margin-bottom: var(--tui-gap-1);
    padding-top: var(--tui-gap-1);
    padding-right: var(--tui-gap-2);
    text-align: left;
    overflow-wrap: break-word;
  }

  &__inner > &__action {
    display: flex;

    &--isStacked {
      flex-direction: column;

      @include tui-stack-vertical(var(--tui-gap-1));
    }
  }
}

.tui-form--horizontal .tui-formRow:not(.tui-formRow--vertical) > {
  .tui-formRow {
    &__inner {
      @include tui-layout-sidebar(
        $side-width: 22rem,
        $content-min-width: 60%,
        $gutter: var(--tui-gap-1),
        $sidebar-selector: '.tui-formRow__desc',
        $content-selector: '.tui-formRow__action'
      );
    }
  }
}
</style>
