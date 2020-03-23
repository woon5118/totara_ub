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
.tui-adder {
  &__list {
    height: 280px;
    margin-top: var(--tui-gap-4);
    overflow-y: scroll;

    &-loadMore {
      margin: var(--tui-gap-1) 0 0;
      text-align: center;
    }
  }

  &__footer {
    display: flex;
    margin-top: var(--tui-gap-4);
    padding-top: var(--tui-gap-6);
    border-top: 1px solid var(--tui-color-neutral-5);
  }

  &__summary {
    display: flex;
    align-items: center;
    width: 50%;
    @include tui-font-heading-label();
  }

  &__actions {
    display: flex;
    justify-content: flex-end;
    width: 50%;
  }
}

@media screen and (min-width: $tui-screen-sm) {
  .tui-adder {
    .tui-tabContent {
      min-height: 380px;
    }

    &__listBasket {
      min-height: 350px;
    }
  }
}
</style>
