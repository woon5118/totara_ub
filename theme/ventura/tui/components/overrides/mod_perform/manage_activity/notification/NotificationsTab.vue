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

  @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-activityNotifications {
  &__header {
    display: flex;
    align-items: baseline;
    margin-top: var(--tui-gap-2); // Check the content tab before changing this.
  }

  &__title {
    @include tui-font-heading-small();
    flex-basis: 100%;
  }

  &__active {
    @include tui-font-heading-label-small();
    margin-right: var(--tui-gap-4);
  }
}
</style>
