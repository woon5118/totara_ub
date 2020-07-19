<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-popoverFrame {
  @include tui-font-body();
  position: relative;
  max-width: 300px;
  // margin must be equal on all 4 sides, and must not change with position
  margin: 10px;
  padding: var(--tui-gap-4);
  background: var(--tui-color-background);
  background-clip: padding-box;
  border: 1px solid var(--tui-color-neutral-5);
  box-shadow: var(--tui-shadow-3);

  &__close {
    position: absolute;
    top: 0;
    right: 0;
    display: flex;
    padding: calc(1.4rem - 0.1em) calc(1.4rem - 0.3em);
  }

  &__title {
    @include tui-font-heading-x-small();
    padding-top: var(--tui-gap-2);
    padding-right: var(--tui-gap-4);
  }

  &__content {
    padding: var(--tui-gap-2) var(--tui-gap-4) var(--tui-gap-2) 0;
  }

  &__title + &__content {
    padding-right: 0;
  }

  &__buttons {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--tui-gap-3);
    padding-bottom: var(--tui-gap-3);

    > * + * {
      margin-left: var(--tui-gap-4);
    }
  }
}
</style>
