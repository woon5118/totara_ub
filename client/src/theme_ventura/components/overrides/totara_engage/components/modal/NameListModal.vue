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

  @author Brian Barnes <brian.barnes@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-engage__components__modal__namelistmodal {
  &-title {
    @include tui-font-heading-x-small();

    margin: 0;
    padding: var(--tui-gap-8) var(--tui-gap-8) var(--tui-gap-2);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-primary);
  }

  &-close {
    position: absolute;
    top: 0;
    right: 0;
    padding: var(--tui-gap-3);
  }

  &-content {
    max-height: 50vh;
    overflow-y: auto;

    &-list {
      @include tui-font-body-small();
      margin: 0;
      color: var(--tui-color-state);
      list-style-type: none;

      li {
        padding: var(--tui-gap-2) var(--tui-gap-8);

        + li {
          border-top: var(--tui-border-width-thin) solid
            var(--tui-color-neutral-5);
        }

        & > :first-child {
          margin-right: var(--tui-gap-1);
        }
      }
    }

    &-loadmore {
      padding: var(--tui-gap-2) var(--tui-gap-8);
      border-top: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    }
  }
}
</style>
