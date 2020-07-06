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
.tui-performAssignmentSchedule {
  width: max-content;

  &__heading {
    @include tui-font-heading-small;
  }

  &__sectionHeading {
    @include tui-font-heading-label;
  }

  &__data {
    margin: var(--tui-gap-5) var(--tui-gap-2) var(--tui-gap-3);
    background: var(--tui-color-neutral-2);
    border: 1px solid var(--tui-color-neutral-5);
    border-radius: 6px;
  }

  &__additional_settings {
    margin-top: var(--tui-gap-5);
  }

  &__action {
    margin-top: var(--tui-gap-5);
  }

  &__radio {
    &-disabled {
      @include tui-font-body-disabled;
    }
  }
}
</style>
