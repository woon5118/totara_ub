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
.tui-repeater {
  display: flex;

  & > * {
    margin-bottom: var(--tui-gap-3);
  }

  &--horizontal {
    flex-wrap: wrap;
    & > * {
      margin-right: var(--tui-gap-4);
    }
  }

  &--noSpacing > * {
    margin: 0;
  }

  &--vertical {
    flex-direction: column;
  }

  &--start {
    & > button:last-child {
      align-self: flex-start;
    }
  }

  &--center {
    & > button:last-child {
      align-self: center;
    }
  }

  &--end {
    & > button:last-child {
      align-self: flex-end;
    }
  }

  &__row {
    display: flex;

    &--vertical {
      &-start {
        justify-content: flex-start;
      }

      &-center {
        justify-content: center;
      }

      &-end {
        justify-content: flex-end;
      }
    }

    &--horizontal {
      &-start {
        align-items: flex-start;
      }

      &-center {
        align-items: center;
      }

      &-end {
        align-items: flex-end;
      }
    }
  }
}
</style>
