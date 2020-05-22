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

  @author Kunle Odusan <kunle.odusan@totaralearning.com>
  @package theme_ventura
-->
<style lang="scss">
.tui-performActivitySectionRelationship {
  > * {
    margin-top: var(--tui-gap-4);
  }
  &__item {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    &-name {
      display: flex;
      flex-grow: 1;
      justify-content: space-between;
      padding: var(--tui-gap-2);
      border: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);
      border-radius: var(--tui-border-radius-normal);
    }
    &-options {
      margin-top: var(--tui-gap-4);
    }
  }
}
@media (min-width: $tui-screen-xs) {
  .tui-performActivitySectionRelationship {
    > * {
      margin-top: var(--tui-gap-2);
    }
    &__item {
      flex-direction: row;
      &-name {
        max-width: 500px;
      }
      &-options {
        margin: auto 0;
        padding: var(--tui-gap-2);
      }
    }
  }
}
</style>
