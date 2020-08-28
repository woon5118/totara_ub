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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-spaceImagePicker {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;

  padding: var(--tui-gap-2);
  border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

  &--loading {
    align-items: center;
    justify-content: center;
    border: none;
  }

  &__image {
    display: flex;
    flex: 1;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;

    width: 100%;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    border-radius: var(--tui-border-radius-normal);

    &--progressing {
      align-items: stretch;
    }

    &__loader {
      display: flex;
      flex: 1;
      flex-direction: column;
      justify-content: center;
    }

    &__button,
    &__progress {
      margin-bottom: var(--tui-gap-4);
    }
  }
}
</style>
