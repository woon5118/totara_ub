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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package mod_perform
-->

<style lang="scss">
.tui_performAssignmentScheduleToggles {
  display: flex;
  flex-wrap: wrap;

  &__setting-group {
    margin-right: var(--tui-gap-2);
  }

  &__toggle-group {
    display: flex;
    flex-wrap: wrap;
  }

  &__toggle-set {
    margin-right: var(--tui-gap-4);
    margin-bottom: var(--tui-gap-2);
  }

  &__toggle-button-group {
    margin: var(--tui-gap-4);
  }

  &__toggle-button {
    min-height: 50px;
  }

  &__button-label {
    margin-top: var(--tui-gap-2);
    font-weight: bold;
  }
}
</style>
