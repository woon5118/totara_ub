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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-formRow {
  display: flex;
  flex-direction: column;
  margin-bottom: var(--tui-gap-4);

  &__action {
    display: flex;

    &--isStacked {
      flex-direction: column;

      & > * + * {
        margin-top: var(--tui-gap-1);
      }
    }
  }

  &__desc {
    display: flex;
    min-width: 0;
    margin-bottom: var(--tui-gap-1);
    text-align: left;
    overflow-wrap: break-word;
  }

  .tui-formLabel,
  .tui-formHelpIcon {
    padding-top: var(--tui-gap-1);
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-form--horizontal .tui-formRow {
    flex-direction: row;

    &__action {
      flex-grow: 1;
      min-width: 0;
      max-width: 400px;
    }

    &__desc {
      flex-shrink: 0;
      width: 40%;
      max-width: 164px;
      margin: 0 var(--tui-gap-4) 0 0;
    }
  }

  .tui-form--inputWidth-limited .tui-formRow {
    input[type='email'].tui-formInput,
    input[type='number'].tui-formInput,
    input[type='password'].tui-formInput,
    input[type='search'].tui-formInput,
    input[type='tel'].tui-formInput,
    input[type='text'].tui-formInput,
    input[type='url'].tui-formInput,
    .tui-select,
    .tui-formTextarea,
    .tui-formFieldError,
    .tui-range {
      width: 280px;
      max-width: 280px;
    }

    .tui-formField {
      width: auto;
    }
  }
}
</style>
