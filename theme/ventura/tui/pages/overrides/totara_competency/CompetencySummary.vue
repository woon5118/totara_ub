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
.tui-competencySummary {
  @include tui-font-body();

  & > * + * {
    margin-top: var(--tui-gap-4);
  }

  &__header {
    &-title {
      @include tui-font-heading-medium();
      margin: var(--tui-gap-2) 0 0;
    }
  }

  &__sectionHeader {
    display: flex;
    align-items: center;
    padding-bottom: var(--tui-gap-1);
    border-bottom: var(--tui-border-width-thin) solid var(--tui-color-neutral-5);

    &-title {
      margin: 0;
      @include tui-font-heading-small();
    }

    &-edit {
      margin-left: auto;
      padding-left: var(--tui-gap-2);
    }
  }
}
</style>
