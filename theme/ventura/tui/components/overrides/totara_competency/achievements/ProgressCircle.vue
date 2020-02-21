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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-progressCircle {
  &__circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 70px;
    height: 70px;
    margin: var(--tui-gap-6) auto 0;
    background: var(--tui-color-neutral-4);
    border-radius: 100%;
  }

  &__label {
    margin-top: var(--tui-gap-1);
    text-align: center;
  }

  &--complete &__circle {
    background: var(--tui-color-primary);
  }
}
</style>
