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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-dataTableExpandableRow {
  position: relative;
  color: var(--tui-datatable-expanded-text-color);
  border: 1px solid var(--tui-datatable-expanded-border-color);
  box-shadow: var(--tui-shadow-2);

  &__close {
    position: absolute;
    top: var(--tui-gap-2);
    right: 0;
    z-index: 2;
    font-size: var(--tui-font-size-20);

    .flex_icon {
      vertical-align: text-top;
    }
  }

  &__content {
    position: relative;
    height: 100vh;
    padding: var(--tui-gap-3);
    overflow-x: hidden;
    overflow-y: scroll;
    background: var(--tui-datatable-expanded-bg-color);
  }

  &__placement {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100vw;
    height: 0;
    padding: 0;
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableExpandableRow {
    margin: var(--tui-gap-2) 0;

    &__content {
      height: inherit;
      overflow-y: hidden;
    }

    &__placement {
      position: relative;
      top: inherit;
      left: 0;
      z-index: initial;
      width: 100%;
      height: inherit;
    }
  }
}
</style>
