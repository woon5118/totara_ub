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
