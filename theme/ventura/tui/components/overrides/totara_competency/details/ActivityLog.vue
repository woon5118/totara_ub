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
.tui-competencyDetailActivityLog {
  margin: var(--tui-gap-2) var(--tui-gap-4);

  &__title {
    @include tui-font-heading-small();
    padding-top: var(--tui-gap-2);
  }

  &__date {
    word-wrap: break-word;
  }

  &__description {
    .flex-icon {
      position: relative;
      top: -1px;
    }

    &-rating {
      font-weight: bold;
    }

    &-tracking {
      font-weight: bold;
      text-transform: uppercase;
    }

    &-system {
      &::before {
        content: '* ';
      }
      &::after {
        content: ' *';
      }
    }
  }
}

@media screen and (min-width: $tui-screen-xs) {
  .tui-competencyDetailActivityLog {
    &__proficient {
      font-weight: bold;
      &-srOnly {
        @include sr-only();
      }
    }
  }
}
</style>
