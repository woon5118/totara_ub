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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
:root {
  --tui-modal-content-separation: var(--tui-gap-2);
  --tui-modal-content-separation-large: var(--tui-gap-8);
  --tui-modal-content-outer-padding: var(--tui-gap-8);
}

.tui-modalContent {
  @include tui-font-body();
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-height: 0;
  padding: var(--tui-modal-content-outer-padding) 0;

  &__header {
    display: flex;
    flex-shrink: 0;
    padding: 0 var(--tui-modal-content-outer-padding);

    &-title {
      @include tui-font-heading-small();
      flex-grow: 1;
      overflow: hidden;

      &--sronly {
        @include sr-only();
      }
    }

    &-close {
      position: absolute;
      top: 0;
      right: 0;
      padding: var(--tui-gap-3);
    }
  }

  &__content {
    flex-grow: 1;
    min-height: 0;
    margin-top: var(--tui-modal-content-separation);
    padding: 0 var(--tui-modal-content-outer-padding);
    overflow-y: auto;
  }

  &__footer {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    margin-top: var(--tui-modal-content-separation-large);
    padding: 0 var(--tui-modal-content-outer-padding);

    &-buttons {
      margin-left: auto;
    }
  }
}
</style>
