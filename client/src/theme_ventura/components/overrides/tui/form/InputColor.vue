<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module theme_ventura
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
