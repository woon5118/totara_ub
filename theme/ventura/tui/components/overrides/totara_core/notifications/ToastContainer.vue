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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package theme_ventura
-->

<style lang="scss">
.tui-toastContainer {
  position: fixed;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: var(--tui-zindex-toast);
  display: flex;
  justify-content: center;
  pointer-events: none;

  &--hasItems &__inner {
    padding-bottom: var(--tui-gap-3);
  }

  &__inner {
    position: relative;
    flex-basis: 700px;
    pointer-events: auto;

    & .notificationBanner + .notificationBanner {
      margin-top: var(--tui-gap-4);
    }
  }

  &__list-item {
    margin: var(--tui-gap-3);
    margin-top: 0;
    transition: all 0.15s ease-out;
  }

  &__list-transition {
    &-enter {
      transform: translateY(calc(var(--tui-gap-12) + var(--tui-gap-3)));
      opacity: 0;
    }

    &-leave {
      transform: none;
    }

    &-leave-to {
      transform: none;
      opacity: 0;
    }

    &-leave-active {
      position: absolute;
      right: 0;
      left: 0;
      transform: none;
      opacity: 0;
    }
  }
}
</style>
