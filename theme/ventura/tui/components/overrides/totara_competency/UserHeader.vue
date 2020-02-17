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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-competencyUserHeader {
  position: relative;
  display: flex;
  align-items: center;

  &::after {
    position: absolute;
    right: 0;
    bottom: 0;
    left: 19px; // half of xsmall avatar (which is specified in px)
    border-bottom: var(--tui-border-width-normal) solid var(--tui-color-primary);
    content: '';
  }

  &__name {
    @include tui-font-body();
    // offset name by the border width so it doesn't look weird
    margin-bottom: var(--tui-border-width-normal);
    margin-left: var(--tui-gap-2);
    font-weight: bold;
  }
}
</style>
