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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-tagList {
  display: flex;
  align-items: flex-start;
  min-width: 230px;
  padding: var(--tui-gap-2);
  border: var(--tui-border-width-thin) solid var(--tui-form-input-border-color);

  &__tags {
    display: flex;
    align-items: center;
    width: 100%;
    min-width: 0;
  }

  &__tagItems {
    display: flex;
    align-items: center;
    min-width: 0;

    &--open {
      flex-wrap: wrap;
    }

    & > * {
      margin-right: var(--tui-gap-1);
      margin-bottom: 0.2rem;
    }
  }

  &__suffix {
    @include tui-font-body-small();
    flex-shrink: 0;
    padding-right: var(--tui-gap-2);
    padding-left: var(--tui-gap-1);
    white-space: nowrap;
  }

  &__input {
    margin: -2px 0;
  }
}
</style>
