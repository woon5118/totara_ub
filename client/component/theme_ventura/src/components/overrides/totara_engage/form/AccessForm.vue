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
.tui-accessForm {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: space-between;
  width: 100%;
  height: 100%;

  &__heading {
    @include tui-font-heading-small();
    margin-bottom: var(--tui-gap-4);
  }

  &__options {
    &--withTagList {
      flex-basis: 35%;
    }

    &--withoutTagLists {
      flex-basis: 90%;
      flex-grow: 1;
    }
  }

  &__time {
    margin-top: var(--tui-gap-4);
  }

  &__tagLists {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    margin-top: var(--tui-gap-4);
  }

  &__tagList {
    margin-bottom: var(--tui-gap-1);
  }

  &__buttons {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--tui-gap-2);

    &__back {
      margin-right: auto;
    }
  }
}
</style>
