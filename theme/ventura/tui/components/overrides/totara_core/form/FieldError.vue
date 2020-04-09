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
.tui-formFieldError {
  margin-top: var(--tui-gap-1);

  &__inner {
    @include tui-font-body-small();
    display: inline-flex;
    align-items: center;
    padding: var(--tui-gap-1) var(--tui-gap-2);
    color: var(--tui-color-neutral-1);
    background: var(--tui-color-prompt-alert);
    border-radius: var(--tui-border-radius-small);
  }

  &__icon {
    width: auto;
    margin-right: var(--tui-gap-1);
  }

  &__dismiss {
    margin-left: var(--tui-gap-1);
    padding: 0;
    color: inherit;
    opacity: 0.7;

    &:hover:active,
    &:active,
    &:hover,
    &:focus {
      color: inherit;
      opacity: 1;
    }

    &-icon {
      position: relative;
      top: -1px;
      @include tui-font-body-small();
    }
  }
}
</style>
