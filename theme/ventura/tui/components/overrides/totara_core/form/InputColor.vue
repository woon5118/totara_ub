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
.tui-colorPicker__group {
  position: relative;

  .tui-colorPicker {
    // visually hide the colour Input control
    &__picker[type='color'] {
      position: absolute;
      width: calc(var(--tui-gap-8) + var(--tui-gap-2));
      opacity: 0;

      &[disabled='disabled'] {
        cursor: not-allowed;
      }
    }

    // quite acute box model and positioning values to make sure they scale with
    // associated text Input values
    &__colorBlock {
      position: absolute;
      // TODO: top/left/height dependent on TL-23877 to fix nested calc() in
      // IE11, otherwise this colour block element will have inaccurate computed
      // values
      // prettier-ignore
      top: calc( var(--tui-form-input-v-padding) / 2 + var(--tui-form-input-border-size) );
      left: calc(var(--tui-gap-2) / 2);
      width: var(--tui-gap-8);
      // prettier-ignore
      height: calc(100% - var(--tui-form-input-v-padding) - var(--tui-form-input-border-size) * 2);
      border-radius: var(--tui-border-radius-small);

      &--disabled {
        cursor: not-allowed;
      }
    }

    &__input[type='text'] {
      padding-left: calc(var(--tui-gap-8) + var(--tui-gap-2));
    }
  }
}
</style>
