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
.tui-popoverFrame {
  @include tui-font-body();
  position: relative;
  max-width: 300px;
  // margin must be equal on all 4 sides, and must not change with position
  margin: 10px;
  padding: var(--tui-gap-4);
  background: var(--tui-color-background);
  background-clip: padding-box;
  border: 1px solid var(--tui-color-neutral-5);
  box-shadow: var(--tui-shadow-3);

  &__close {
    position: absolute;
    top: 0;
    right: 0;
    display: flex;
    padding: calc(1.4rem - 0.1em) calc(1.4rem - 0.3em);
  }

  &__title {
    @include tui-font-heading-x-small();
    padding-top: var(--tui-gap-2);
    padding-right: var(--tui-gap-4);
  }

  &__content {
    padding: var(--tui-gap-2) var(--tui-gap-4) var(--tui-gap-2) 0;
  }

  &__title + &__content {
    padding-right: 0;
  }

  &__buttons {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--tui-gap-3);
    padding-bottom: var(--tui-gap-3);

    > * + * {
      margin-left: var(--tui-gap-4);
    }
  }
}
</style>
