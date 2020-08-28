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
.tui-playlistForm {
  display: flex;
  flex: 1;
  flex-direction: column;
  margin-top: var(--tui-gap-8);

  &__title {
    // Reset margin of form row.
    &.tui-formRow {
      margin-bottom: 0;
    }

    .tui-formRow__desc {
      // No description/label for this form row, so we just make its margin to be zero, save some spaces.
      margin: 0;
    }
  }

  &__description {
    display: flex;
    flex: 2;
    flex-direction: column;
    margin-top: var(--tui-gap-8);

    &__tip {
      position: relative;
      display: flex;

      &__content {
        margin: 0;
      }
    }

    &__formRow {
      // Making the form row to be expanded
      flex: 1;

      .tui-formRow {
        &__desc {
          // Save some space here, as there are no description/label.
          margin: 0;
        }

        &__action {
          display: flex;
          flex: 1;
          flex-direction: column;
        }
      }

      &__textArea {
        flex: 1;
      }
    }
  }

  &__buttons {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--tui-gap-8);
  }
}
</style>
