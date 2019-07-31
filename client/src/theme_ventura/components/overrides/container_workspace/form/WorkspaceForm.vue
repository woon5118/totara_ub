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
.tui-workspaceForm {
  display: flex;
  flex-direction: column;
  justify-content: space-between;

  &__container {
    display: flex;
    flex-grow: 1;
    align-items: stretch;

    &__inputs {
      display: flex;
      flex-direction: column;
      width: 66%;

      &__formRow {
        // Overriding the margin
        &.tui-formRow {
          margin-bottom: 0;

          &:not(:first-child) {
            margin-top: var(--tui-gap-8);
          }
        }

        .tui-formRow__desc {
          margin-bottom: var(--tui-gap-2);
        }

        &__editor {
          display: flex;
          flex-direction: column;
          flex-grow: 1;
          width: 100%;

          &__tip {
            display: flex;
            margin-top: var(--tui-gap-2);

            &__text {
              @include tui-font-body-small();
            }
          }
        }
      }

      &__unhiddenRow {
        margin-top: var(--tui-gap-8);
        &__box {
          display: flex;
          flex-direction: column;
          flex-grow: 1;
          width: 100%;

          &__helpText {
            @include tui-font-body-small();
            margin: 0;
            margin-top: var(--tui-gap-2);
          }
        }
      }
    }

    &__imagePicker {
      // This will let us to have our custom FORM input :)
      width: calc(100% - (66% + var(--tui-gap-4)));

      &.tui-formRow {
        // Reset margin
        margin: 0;
        margin-left: var(--tui-gap-4);

        .tui-formRow {
          &__desc {
            // Hiding description part.
            display: none;
          }

          &__action {
            display: flex;
            flex-direction: column;
            height: 100%;
          }
        }
      }
    }
  }

  &__buttonGroup {
    display: flex;
    justify-content: flex-end;

    // This button group need to have the same width as the inputs div
    width: 66%;
    margin-top: var(--tui-gap-8);

    &.tui-formBtnGroup {
      // Overriding the margin
      & > :not(:first-child) {
        margin: 0;
        margin-left: var(--tui-gap-4);
      }
    }
  }
}
</style>
