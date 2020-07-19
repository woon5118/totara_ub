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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-checkboxButton {
  $block: #{&};
  position: relative;
  display: flex;

  &__input {
    position: absolute;
    opacity: 0;
  }

  &__label {
    @include tui-font-body-small();
    display: flex;
    flex-grow: 1;
    margin: 0;
    padding: var(--tui-gap-2);
    color: var(--tui-btn-checkbox-text-color);
    font-weight: normal;
    border-radius: 6px;
    cursor: pointer;

    &:hover {
      color: var(--tui-btn-checkbox-text-color-focus);
      background: var(--tui-btn-checkbox-bg-color-hover);
    }

    &:active:hover,
    &:active {
      color: var(--tui-btn-checkbox-text-color-active);
    }
  }

  &__deselectIcon {
    margin: auto 0 auto auto;
    color: var(--tui-btn-checkbox-text-color-selected);
    visibility: hidden;
  }

  &__input:checked ~ &__label {
    color: var(--tui-btn-checkbox-text-color-selected);
    background: var(--tui-btn-checkbox-bg-color-selected);

    #{$block}__deselectIcon {
      visibility: visible;
    }

    &:hover {
      color: var(--tui-btn-checkbox-text-color-focus);
      background: var(--tui-btn-checkbox-bg-color-hover);

      #{$block}__deselectIcon {
        color: var(--tui-btn-checkbox-text-color-focus);
        visibility: visible;
      }
    }

    &:active:hover,
    &:active {
      color: var(--tui-btn-checkbox-text-color-active);
      background: var(--tui-btn-checkbox-bg-color-hover);

      #{$block}__deselectIcon {
        color: var(--tui-btn-checkbox-text-color-active);
        visibility: visible;
      }
    }
  }

  &__input:focus ~ &__label {
    @include tui-focus();
  }

  &__input:focus:checked ~ &__label {
    @include tui-focus();

    #{$block}__deselectIcon {
      color: var(--tui-btn-checkbox-text-color-selected);
      visibility: visible;
    }
  }
}
</style>
