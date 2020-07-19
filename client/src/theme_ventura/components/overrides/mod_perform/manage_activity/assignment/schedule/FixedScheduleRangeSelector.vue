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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-performAssignmentScheduleFixedDateSelector {
  display: inline-flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-content: space-between;
  align-items: baseline; // Before changing this to center or something else, check the validation errors line up ok.
  justify-content: flex-start;

  &__input {
    display: inline-flex;
    flex-basis: 100%;
    flex-direction: row;
    flex-wrap: wrap;
    align-content: space-between;
    align-items: baseline; // Before changing this to center or something else, check the validation errors line up ok.
    justify-content: flex-start;
    margin-top: var(--tui-gap-1);

    @media (min-width: $tui-screen-md) {
      flex-basis: auto;
      flex-wrap: nowrap;
    }
  }

  div.tui-dateSelector__date {
    margin-left: 0;
  }

  @media (min-width: $tui-screen-sm) {
    div.tui-dateSelector__time {
      margin-right: var(--tui-gap-2);
      margin-left: var(--tui-gap-2);
    }
  }

  @media (min-width: $tui-screen-md) {
    &--has-range {
      div.tui-dateSelector__date {
        margin-right: var(--tui-gap-2);
        margin-left: var(--tui-gap-2);
      }
    }
  }

  &__label.tui-formLabel {
    font-weight: normal;
    word-break: break-word;

    @media (min-width: $tui-screen-md) {
      min-width: unset;
      word-break: normal;
    }
  }
}
</style>
