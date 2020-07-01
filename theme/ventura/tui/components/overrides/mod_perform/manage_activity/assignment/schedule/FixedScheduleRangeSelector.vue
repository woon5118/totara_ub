<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package theme_ventura
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
