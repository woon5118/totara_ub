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
// Reset
input[type='date'].tui-formInput,
input[type='datetime-local'].tui-formInput,
input[type='email'].tui-formInput,
input[type='month'].tui-formInput,
input[type='number'].tui-formInput,
input[type='password'].tui-formInput,
input[type='search'].tui-formInput,
input[type='tel'].tui-formInput,
input[type='text'].tui-formInput,
input[type='time'].tui-formInput,
input[type='url'].tui-formInput,
input[type='week'].tui-formInput {
  display: inline-block;
  width: auto;
  max-width: none;
  height: auto;
  max-height: none;
  margin: 0;
  padding: 1px;
  color: rgb(0, 0, 0);
  font-size: inherit;
  line-height: inherit;
  letter-spacing: normal;
  text-align: start;
  text-transform: none;
  text-indent: 0;
  text-shadow: none;
  word-spacing: normal;
  background-color: rgb(255, 255, 255);
  border-color: rgb(218, 218, 218);
  border-style: inset;
  border-width: 2px;
  border-radius: 0;
  border-image-source: none;
  border-image-slice: 100%;
  border-image-width: 1;
  border-image-outset: 0;
  border-image-repeat: stretch;
  border-spacing: 0;
  box-shadow: none;
  cursor: text;
  transition-delay: 0s;
  transition-timing-function: ease;
  transition-duration: 0s;
  transition-property: all;
  text-rendering: auto;

  &[disabled] {
    color: rgb(61, 68, 75);
    background: rgb(218, 218, 218);
  }

  &:focus {
    border-color: rgb(218, 218, 218);
    outline-width: 3px;
    outline-style: auto;
    outline-color: Highlight;
    outline-color: -webkit-focus-ring-color;
    outline-offset: -2px;
    box-shadow: none;
    -moz-user-focus: normal;
  }

  &::placeholder {
    color: #a9a9a9;
    opacity: 1;
  }
}

input[type='date'].tui-formInput,
input[type='datetime-local'].tui-formInput,
input[type='email'].tui-formInput,
input[type='month'].tui-formInput,
input[type='number'].tui-formInput,
input[type='password'].tui-formInput,
input[type='search'].tui-formInput,
input[type='tel'].tui-formInput,
input[type='text'].tui-formInput,
input[type='time'].tui-formInput,
input[type='url'].tui-formInput,
input[type='week'].tui-formInput {
  display: block;
  flex-grow: 1;
  box-sizing: border-box;
  width: 100%;
  min-width: 0;
  height: var(--tui-form-input-height);
  padding: var(--tui-form-input-v-padding) var(--tui-gap-2);
  color: var(--tui-form-input-text-color);
  font-size: var(--tui-form-input-font-size);
  line-height: 1;
  background: var(--tui-form-input-bg-color);
  border: var(--tui-form-input-border-size) solid;
  border-color: var(--tui-form-input-border-color);

  .tui-context-invalid & {
    border-color: var(--tui-form-input-border-color-invalid);
  }

  &::placeholder {
    color: var(--tui-form-input-text-placeholder-color);
  }

  &:focus {
    background: var(--tui-form-input-bg-color-focus);
    border: var(--tui-form-input-border-size) solid;
    border-color: var(--tui-form-input-border-color-focus);
    outline: none;
    box-shadow: var(--tui-shadow-1);

    .tui-context-invalid & {
      background: var(--tui-form-input-bg-color-invalid-focus);
      border-color: var(--tui-form-input-border-color-invalid);
      box-shadow: var(--tui-shadow-1-invalid);
    }
  }

  &--preIcon {
    padding-left: var(--tui-gap-6);
  }

  &--transparent,
  &--transparent:focus {
    background-color: transparent;
    border: none;
    box-shadow: none;
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
