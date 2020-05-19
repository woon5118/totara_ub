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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->

<style lang="scss">
.tui_performAssignmentSchedule {
  &__range-type-heading {
    margin-top: var(--tui-gap-4);
  }

  &__range-type-settings {
    padding: var(--tui-gap-2);
    background-color: var(--tui-color-neutral-3);
  }

  &__data {
    margin: var(--tui-gap-5) var(--tui-gap-2) var(--tui-gap-3);
    background: var(--tui-color-neutral-2);
    border: 1px solid var(--tui-color-neutral-5);
    border-radius: 6px;
  }

  &__action {
    margin-top: var(--tui-gap-5);
  }

  &__narrative-select {
    width: 100px;
  }

  &__narrative-inputs {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-content: space-between;
    align-items: baseline; // Before changing this to center or something else, check the validation errors line up ok.
    justify-content: flex-start;

    // Fighting for specificity with .tui-formLabel on margin.
    .tui_performAssignmentSchedule__narrative-label {
      margin-right: var(--tui-gap-2);
      margin-left: var(--tui-gap-2);
      font-weight: normal;

      &.tui-formLabel {
        font-weight: normal;
      }

      &:first-child {
        margin-left: 0;
      }
    }
  }

  &__narrative-count {
    max-width: 50px;
    margin-bottom: var(--tui-gap-2);

    .tui-formFieldError__inner {
      min-width: 90px;
    }
  }

  &__narrative-date {
    max-width: 100px;
    margin-bottom: var(--tui-gap-2);

    .tui-formFieldError__inner {
      min-width: 100px;
    }
  }

  &__narrative-select {
    max-width: 100px;
    margin-bottom: var(--tui-gap-2);
    margin-left: var(--tui-gap-2);
  }
}
</style>
