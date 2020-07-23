<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module theme_ventura
-->

<style lang="scss">
.tui-toggleBtn {
  position: relative;
  display: inline-block;
  flex-shrink: 0;
  min-width: var(--tui-gap-12);
  max-width: 100%;
  height: var(--tui-form-input-height);
  padding: 0 var(--tui-gap-2);
  color: var(--tui-toggle-btn-text-color);
  font-size: var(--tui-form-input-font-size);
  line-height: 1.2;
  background: var(--tui-toggle-btn-bg-color);
  border: none;
  border-radius: calc(var(--tui-btn-radius) - 1px);

  &:focus,
  &:hover {
    color: var(--tui-toggle-btn-text-color-focus);
    text-decoration: none;
    background: var(--tui-toggle-btn-bg-color-focus);
    box-shadow: none;
  }

  &:active,
  &:active:focus,
  &:active:hover {
    color: var(--tui-toggle-btn-text-color-active);
    text-decoration: none;
    background: var(--tui-toggle-btn-bg-color-active);
    border: none;
    box-shadow: none;
  }

  &:active:focus,
  &:focus {
    outline: 1px dashed var(--tui-toggle-btn-text-color-active);
    outline-offset: -3px;
  }

  &[disabled] {
    color: var(--tui-toggle-btn-text-color-disabled);
    background: var(--tui-toggle-btn-bg-color-disabled);
    cursor: default;

    &:focus,
    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-toggle-btn-text-color-disabled);
      background: var(--tui-toggle-btn-bg-color-disabled);
    }
  }

  &--selected {
    color: var(--tui-toggle-btn-text-color-selected);
    background: var(--tui-toggle-btn-bg-color-selected);
    cursor: default;

    &:focus,
    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-toggle-btn-text-color-selected);
      background: var(--tui-toggle-btn-bg-color-selected);
    }

    &:active:focus,
    &:focus {
      outline: 1px dashed var(--tui-toggle-btn-text-color-selected);
      outline-offset: -3px;
    }
  }

  &[disabled]&--selected {
    color: var(--tui-toggle-btn-text-color-selected);
    background: var(--tui-toggle-btn-bg-color-selected);

    &:focus,
    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      color: var(--tui-toggle-btn-text-color-selected);
      background: var(--tui-toggle-btn-bg-color-selected);
    }
  }

  &--hasText {
    min-width: 70px;

    .tui-toggleBtn__icon {
      font-size: var(--tui-font-size-14);
    }
  }

  &--large {
    min-width: 10rem;
    min-height: 4.6rem;

    .tui-toggleBtn__content {
      flex-direction: column;

      & > * + * {
        margin-top: var(--tui-gap-1);
      }
    }
  }

  &__content {
    display: inline-flex;
    align-items: center;
  }

  &__icon {
    position: relative;
    top: 1px;
    display: flex;
    flex-shrink: 0;
    font-size: var(--tui-font-size-15);
  }

  &__text {
    padding: 0 var(--tui-gap-1);
  }
}
</style>
