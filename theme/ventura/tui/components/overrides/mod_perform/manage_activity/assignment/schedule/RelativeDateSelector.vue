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
.tui-performAssignmentScheduleRelativeDateSelector {
  display: inline-flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-content: space-between;
  align-items: baseline; // Before changing this to center or something else, check the validation errors line up ok.
  justify-content: flex-start;

  & > *:not(:last-child) {
    margin-top: var(--tui-gap-1);
    margin-right: var(--tui-gap-1);
  }

  &__count {
    max-width: 5rem;
  }

  &__unit,
  &__direction {
    max-width: 10rem;
  }

  &__reference-date {
    display: flex;
    flex-direction: row;
    &-customSettings {
      min-width: 20rem;
      margin-left: var(--tui-gap-1);
    }
  }

  .tui-formFieldError__inner {
    min-width: 20rem; // Should find a way to handle this better.
    margin-top: var(--tui-gap-1);
  }
}
</style>
