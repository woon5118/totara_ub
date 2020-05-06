<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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
  @package theme_ventura
-->

<style lang="scss">
.mod-perform-activitySection {
  padding: var(--tui-gap-6);

  &.tui-card {
    display: block;
  }

  &__action-buttons {
    text-align: right;
  }

  &__participant-heading {
    @include totara-style-heading-label-small;
    border-bottom: 1px solid var(--tui-card-border-color);
  }

  &__participant-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--tui-gap-2);
    padding: var(--tui-gap-2);
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
    border-radius: var(--tui-border-radius-normal);
  }
}
</style>
