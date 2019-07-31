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
.tui-totaraEngage-surveyForm {
  display: flex;
  flex: 1;
  flex-direction: column;
  margin-top: var(--tui-gap-8);

  &__title {
    display: flex;
    margin-bottom: var(--tui-gap-8);
  }

  &__optionType {
    width: 200px;
  }

  &__options {
    margin-top: var(--tui-gap-8);

    &__input {
      display: flex;
    }
  }

  // Overiding css.
  .tui-formRow__desc {
    margin-bottom: 0;
  }

  &__answerTitle {
    // Overiding css.
    .tui-formRow {
      .tui-formRow__desc {
        margin-bottom: var(--tui-gap-2);
      }
    }
  }

  &__repeater {
    .tui-repeater__row {
      .tui-totaraEngage-surveyForm__repeater__input {
        width: 80%;
        .tui-formField {
          width: 100%;
          input[type='text'].tui-formInput {
            width: 100%;
            max-width: 100%;
          }
        }
      }
    }
  }

  &__buttons {
    flex: 1;
    align-items: flex-end;
    margin-top: var(--tui-gap-2);
    button.tui-totaraEngage-surveyForm__cancelButton {
      margin-bottom: 0;
    }
    &--left {
      justify-content: flex-start;
    }
    &--right {
      justify-content: flex-end;
    }
  }
}
</style>
