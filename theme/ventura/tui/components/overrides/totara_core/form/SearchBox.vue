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
.tui-searchBox {
  display: flex;

  .tui-formLabel {
    margin-right: var(--tui-gap-2);
  }

  // So that the search button matches the format of the input that is next to it
  &__button {
    border-color: var(--tui-form-input-border-color);
    border-style: solid;
    border-width: var(--tui-form-input-border-size)
      var(--tui-form-input-border-size) var(--tui-form-input-border-size) 0;

    &:hover,
    &:active,
    &:focus {
      background-color: var(--tui-btn-bg-color-focus);
      border-color: var(--tui-form-input-border-color);
      border-style: solid;
      border-width: var(--tui-form-input-border-size)
        var(--tui-form-input-border-size) var(--tui-form-input-border-size) 0;
      box-shadow: var(--tui-btn-shadow-focus);
    }
  }
}
</style>
