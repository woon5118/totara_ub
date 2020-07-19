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
.tui-separator {
  margin: var(--tui-gap-4) 0;

  &--spread {
    margin: var(--tui-gap-8) 0;
  }

  // simple implementation using a horizontal rule element
  &-rule {
    height: var(--tui-border-width-thin);
    line-height: var(--tui-border-width-thin);
    background-color: var(--tui-color-primary);
    border: none;

    &.tui-separator--thick {
      height: var(--tui-border-width-thick);
      line-height: var(--tui-border-width-thick);
    }
  }

  // alternative implementation with wrapper markup to center slotted content
  // horizontal rule visible only to screen readers, the visual lines added with
  // pseudo selectors
  &--wrapped {
    display: flex;
    align-items: center;

    .tui-separator-content {
      margin: 0 var(--tui-gap-4);
      white-space: nowrap;
      text-align: center;
    }

    &:before,
    &:after {
      width: 50%;
      height: var(--tui-border-width-thin);
      line-height: var(--tui-border-width-thin);
      background-color: var(--tui-color-primary);
      content: '';
    }

    &.tui-separator--thick:before,
    &.tui-separator--thick:after {
      height: var(--tui-border-width-thick);
      line-height: var(--tui-border-width-thick);
    }
  }
}
</style>
