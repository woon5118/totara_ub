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
.tui-formTextarea {
  display: block;
  flex-grow: 1;
  width: 100%;
  min-width: 0;
  max-width: 100%;
  max-height: 100%;
  margin: 0;
  padding: var(--tui-gap-1) var(--tui-gap-2);
  overflow: auto;
  color: var(--tui-form-input-text-color);
  font-size: var(--tui-form-input-font-size);
  font-family: inherit;
  line-height: inherit;
  border: var(--tui-form-input-border-size) solid
    var(--tui-form-input-border-color);
  resize: none;

  &::placeholder {
    color: var(--tui-form-input-text-placeholder-color);
  }

  &:focus {
    background: var(--tui-form-input-bg-color-focus);
    border: var(--tui-form-input-border-size) solid
      var(--tui-form-input-border-color-focus);
    outline: none;
    box-shadow: var(--tui-shadow-1);
  }

  &[disabled] {
    color: var(--tui-form-input-text-color-disabled);
    background: var(--tui-form-input-bg-color-disabled);
    border-color: var(--tui-form-input-border-color-disabled);

    &::placeholder {
      color: var(--tui-form-input-text-color-disabled);
    }
  }
}
</style>
