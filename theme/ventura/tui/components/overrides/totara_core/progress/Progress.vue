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
.tui-progress {
  @include tui-font-body-xx-small();

  display: flex;
  height: 14px;
  background-color: var(--tui-progress-container-bg-color);
  border: 1px solid var(--tui-progress-border-color);
  border-radius: var(--tui-gap-2);

  &--small {
    height: 12px;
    line-height: 10px;
  }

  &__bar {
    display: flex;
    justify-content: flex-end;
    background-color: var(--tui-progress-value-bg-color);
    border-radius: var(--tui-gap-2);
    transition: width var(--tui-transition-progress-function)
      var(--tui-transition-progress-duration);
  }

  &__label {
    padding: 0 var(--tui-gap-1);
    opacity: 0;

    &--inside {
      color: var(--tui-progress-value-text-color);
      opacity: 1;
      transition: opacity var(--tui-transition-progress-function)
        var(--tui-transition-progress-duration);
    }
    &--outside {
      color: var(--tui-progress-container-text-color);
      opacity: 1;
    }
  }
}
</style>
