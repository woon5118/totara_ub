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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
@mixin generate-arrow-base($sel, $size) {
  #{$sel}--top,
  #{$sel}--bottom {
    /*rtl:ignore*/
    left: 50%;
    width: $size * 2;
    height: $size * 2;
    /*rtl:ignore*/
    margin-left: -$size;
  }

  #{$sel}--top {
    top: -($size * 2);
  }

  #{$sel}--bottom {
    bottom: -($size * 2);
  }

  #{$sel}--left,
  #{$sel}--right {
    top: 50%;
    width: $size * 2;
    height: $size * 2;
    margin-top: -$size;
  }

  #{$sel}--left {
    left: -($size * 2);
  }

  #{$sel}--right {
    right: -($size * 2);
  }
}

@mixin generate-arrow-part($sel, $sel_2, $size, $offset, $color) {
  #{$sel}#{$sel_2} {
    position: absolute;
    display: block;
    border: $size solid transparent;
    content: '';
  }

  #{$sel}--top#{$sel_2} {
    top: $offset * 2;
    left: $offset;
    border-bottom-color: $color;
  }

  #{$sel}--bottom#{$sel_2} {
    bottom: $offset * 2;
    left: $offset;
    border-top-color: $color;
  }

  #{$sel}--left#{$sel_2} {
    top: $offset;
    left: $offset * 2;
    border-right-color: $color;
  }

  #{$sel}--right#{$sel_2} {
    top: $offset;
    right: $offset * 2;
    border-left-color: $color;
  }
}

.tui-arrow {
  position: absolute;
  pointer-events: none;
}

@include generate-arrow-base('.tui-arrow--normal.tui-arrow', 10px);
@include generate-arrow-part(
  '.tui-arrow--normal.tui-arrow',
  '::before',
  10px,
  0,
  var(--tui-arrow-border-color)
);
@include generate-arrow-part(
  '.tui-arrow--normal.tui-arrow',
  '::after',
  8px,
  2px,
  var(--tui-arrow-bg-color)
);

@include generate-arrow-base('.tui-arrow--large.tui-arrow', 14px);
@include generate-arrow-part(
  '.tui-arrow--large.tui-arrow',
  '::before',
  14px,
  0,
  var(--tui-arrow-border-color)
);
@include generate-arrow-part(
  '.tui-arrow--large.tui-arrow',
  '::after',
  12px,
  2px,
  var(--tui-arrow-bg-color)
);
</style>
