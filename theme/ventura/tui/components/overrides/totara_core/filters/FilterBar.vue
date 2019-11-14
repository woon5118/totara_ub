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
.tui-filterBar {
  display: flex;
  flex-direction: column;
  padding: 0 var(--tui-gap-3);

  &--hasTop {
    padding-top: var(--tui-gap-4);
    border-top: var(--tui-border-width-normal) solid
      var(--tui-filter-bar-border-color);
  }

  &--hasBottom {
    padding-bottom: var(--tui-gap-4);
    border-bottom: var(--tui-border-width-normal) solid
      var(--tui-filter-bar-border-color);
  }

  &__heading {
    @include sr-only();
  }

  &__toggle {
    display: flex;
    justify-content: center;

    &-btn {
      margin: var(--tui-gap-2) 0;
    }
  }

  &__filters {
    $stacked: #{&}--stacked;
    display: flex;
    flex-grow: 1;

    &-left,
    &-right {
      display: flex;
      flex-shrink: 0;

      > * {
        flex-shrink: 0;
      }

      > * + * {
        margin-left: var(--tui-gap-4);
      }

      // make slider icon closer to next filter item
      > .tui-filterBar__filters-icon + * {
        margin-left: var(--tui-gap-2);
      }

      #{$stacked} & {
        flex-direction: column;
        margin-left: 0;

        .tui-formLabel {
          @include tui-font-heading-label-small();
        }

        > * {
          margin-top: var(--tui-gap-2);
          margin-left: 0;
        }
      }
    }

    &-right {
      flex-grow: 1;
      justify-content: flex-end;
      margin-left: var(--tui-gap-4);
    }

    &-icon {
      display: flex;
      align-items: center;
    }

    &-iconSlider {
      display: flex;
      margin: auto 0;
    }

    &--stacked {
      flex-direction: column;
    }
  }
}
</style>
