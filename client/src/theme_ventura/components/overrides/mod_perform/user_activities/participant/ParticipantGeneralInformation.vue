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

  @author Arshad Anwer <arshad.anwer@totaralearning.com>
  @module mod_perform
-->

<style lang="scss">
.tui-participantGeneralInformation {
  @include tui-font-body();

  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border: var(--tui-border-width-thin) solid var(--tui-color-border);
  border-radius: var(--tui-border-radius-normal);

  &__userDetails {
    min-width: 30%;
    height: 100%;
    padding: var(--tui-gap-2) var(--tui-gap-4);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
  }

  &__relationship {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: var(--tui-gap-2) var(--tui-gap-4) var(--tui-gap-1) var(--tui-gap-4);
    background: var(--tui-color-neutral-2);
    border-radius: var(--tui-border-radius-normal);
  }

  &__relationship-heading {
    @include tui-font-heading-small-regular();

    display: flex;
    flex-wrap: wrap;
    margin-bottom: var(--tui-gap-1);

    span {
      @include tui-font-heading-small();
      // margin-left: 4px;
    }
  }

  &__relationshipHeadingDetails {
    @include tui-font-heading-small();
  }

  &__relationship-toSubject {
    @include tui-font-body-small();
    margin-bottom: var(--tui-gap-1);
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-participantGeneralInformation {
    flex-direction: row;

    &__userDetails {
      border-right: var(--tui-border-width-thin) solid
        var(--tui-color-neutral-5);
      border-bottom: none;
    }

    &__relationship-heading {
      span {
        margin-left: var(--tui-gap-1);
      }
    }
  }
}
</style>
