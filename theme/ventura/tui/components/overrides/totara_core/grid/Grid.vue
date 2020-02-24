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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
@mixin grid-item-generate-gutters(
  $_borderType: left,
  $_gutterSize: var(--tui-grid-gutter)
) {
  > .tui-grid-item {
    // because we use transparent borders for gutters but don't want that
    // counting as visible item width
    box-sizing: content-box;
    background-clip: padding-box;
    // reset all possibly existing border widths, it is assumed that grid gaps
    // are unidirectional
    border-width: 0;
    border-#{$_borderType}: #{$_gutterSize} solid transparent;
  }

  // the "first" item should never have a gutter, but `:first-child` may not be
  // accurate if flex re-ordering has been applied, so use generated "first"
  // className instead. this works in most cases with the exception of:
  //  - when the browser ignores the order property due to siblings not having
  //    an order property also
  > .tui-grid-item--first {
    border-width: 0;
  }
}

// Grid styles
.tui-grid {
  display: flex;
  flex-grow: 1; // in case nested inside a parent grid cell
  margin: 0;
  padding: 0;

  // main Grid modifiers applied based on supplied prop values
  &--wrapped {
    flex-wrap: wrap;
  }

  // content-containing elements
  &-item {
    flex-grow: 0; // by default we want item size to respect unit-based calculations
    flex-shrink: 1; // by default we want to auto-adjust for gutters
    min-width: 0; // allows flex items to shrink below their minimum content size
    margin: 0;
    padding: 0;
    hyphens: auto; // default prevents text from causing grid mis-alignments
    list-style-type: none;

    // Grid item modifiers based on supplied prop values
    &--grow {
      flex-grow: 1;
    }
    &--no-shrink {
      flex-shrink: 0;
    }
    &--no-hyphens {
      hyphens: none;
    }
    &--overflow {
      overflow: auto;
    }
  }

  // horizontal grid
  &--horizontal {
    flex-direction: row;

    &-gap {
      @include grid-item-generate-gutters(left, var(--tui-grid-gutter));
    }
  }

  // vertical grid
  &--vertical {
    flex-direction: column;

    &-gap {
      @include grid-item-generate-gutters(top, var(--tui-grid-gutter));
    }
  }

  &--wrapped-gap .tui-grid-item--wrapped {
    // margin better to use here instead of borders, as natural grid gap is
    // assumed to be unidirectional, whereas this additional type of grid gap
    // only applies when grid items wrap, and if the gap is confgured as
    // desirable for a given grid
    margin-top: var(--tui-grid-gutter);
  }

  // switch to stacked display at an container-based pixel width breakpoint
  // value (class is conditionally applied during Grid render())
  &--stacked {
    display: block;

    > .tui-grid-item {
      flex-basis: auto;
    }

    &-gap {
      @include grid-item-generate-gutters(top, var(--tui-grid-gutter));
    }
  }
}
</style>
