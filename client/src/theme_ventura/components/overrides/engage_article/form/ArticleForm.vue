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
.tui-articleForm {
  display: flex;
  flex: 1;
  flex-direction: column;
  margin-top: var(--tui-gap-4);

  &__title {
    &.tui-formRow {
      // Reset form row margin.
      margin-bottom: 0;
    }
    // Reset the margin of label section when it is hidden. So that it does not give us any extra spaces.
    .tui-formRow {
      &__desc {
        margin: 0;
      }
    }
  }

  &__description {
    display: flex;
    flex: 1;
    flex-direction: column;
    margin-top: var(--tui-gap-8);

    &__formRow {
      flex: 1;
      // Reset margin of itself.
      &.tui-formRow {
        margin-bottom: 0;
      }

      .tui-formRow {
        // Reset the margin of label section when it is hidden. So that it does not give us any extra spaces.
        &__desc {
          margin: 0;
        }

        &__action {
          // Expand the box.
          flex: 1;
        }
      }
    }

    &__tip {
      position: relative;
      display: flex;
      margin-top: var(--tui-gap-2);

      &__content {
        margin: 0;
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
