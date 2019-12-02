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
.tui-popoverPositioner {
  // note: position: absolute + z-index triggers a new stacking context
  // this allows us to escape from overflow: hidden.
  // because stacking contexts can be nested, this will still work correctly
  // even inside modals, which have a higher z-index
  position: absolute;
  top: 0;
  /*rtl:ignore*/
  left: 0;
  z-index: var(--tui-zindex-popover);
  width: auto;
  @include tui-font-body();

  &--transition-default {
    transition: opacity 0.2s;
  }

  &--transition-default-enter,
  &--transition-default-leave-to {
    opacity: 0;
  }
}
</style>
