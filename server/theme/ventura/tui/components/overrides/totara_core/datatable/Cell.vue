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
.tui-dataTableCell {
  /* ensure excessively long words don't push out cell width */
  min-width: 0;
  /* ensure excessively long words don't overflow */
  word-wrap: break-word;

  &__label {
    display: inline-block;
    margin-top: var(--tui-gap-2);
    padding: var(--tui-gap-1) 0 var(--tui-gap-2);
    @include tui-font-heading-label-small();
  }
}

@media (min-width: $tui-screen-xs) {
  .tui-dataTableCell {
    display: flex;
    flex-basis: 0;
    flex-direction: column;
    flex-grow: 1;

    &__label {
      display: none;
    }

    &--repeatedHeader &__content {
      @include sr-only();
    }

    &--size {
      &_1 {
        flex-grow: 1;
      }
      &_2 {
        flex-grow: 2;
      }
      &_3 {
        flex-grow: 3;
      }
      &_4 {
        flex-grow: 4;
      }
      &_5 {
        flex-grow: 5;
      }
      &_6 {
        flex-grow: 6;
      }
      &_7 {
        flex-grow: 7;
      }
      &_8 {
        flex-grow: 8;
      }
      &_9 {
        flex-grow: 9;
      }
      &_10 {
        flex-grow: 10;
      }
      &_11 {
        flex-grow: 11;
      }
      &_12 {
        flex-grow: 12;
      }
      &_13 {
        flex-grow: 13;
      }
      &_14 {
        flex-grow: 14;
      }
      &_15 {
        flex-grow: 15;
      }
      &_16 {
        flex-grow: 16;
      }
    }

    &--align {
      &-center {
        align-items: center;
      }

      &-end {
        align-items: flex-end;
      }
    }

    &--heavy {
      @include tui-font-heavy();
    }

    &--valign {
      &-center {
        justify-content: center;
      }

      &-end {
        justify-content: flex-end;
      }
    }
  }
}
</style>
