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
.tui-loader {
  $loading-fullpage: #{&}--fullpage;

  position: relative;

  // Don't show nested loaders
  &--active > * .tui-loader__overlay {
    display: none;
  }

  // If no slot content (e.g. initial load)
  &__empty {
    min-height: var(--tui-gap-8);
  }

  &__overlay {
    position: absolute;
    top: 0;
    /*rtl:ignore*/
    left: 0;
    width: 100%;
    height: 100%;

    #{$loading-fullpage} & {
      position: fixed;
      z-index: var(--tui-zindex-loading-page);
    }

    &-positioner {
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      background: var(--tui-color-background);
      opacity: 0.85;
      content: '';
    }
  }

  &__display {
    position: sticky;
    top: var(--tui-gap-6);
    bottom: var(--tui-gap-6);
    font-size: var(--tui-font-size-18);

    .fa-spinner {
      position: relative;
      top: -1px;
    }
  }
}
</style>
