<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_core
-->

<template>
  <button
    class="tui-iconBtn"
    :aria-describedby="ariaDescribedby"
    :aria-expanded="ariaExpanded"
    :aria-label="ariaLabel"
    :autofocus="autofocus"
    :class="{
      'tui-formBtn--alert': styleclass.alert,
      'tui-iconBtn--prim': styleclass.primary,
      'tui-iconBtn--small': styleclass.small,
      'tui-iconBtn--circle': styleclass.circle,
      'tui-iconBtn--hasText': text,
      'tui-iconBtn--square': styleclass.square,
      'tui-iconBtn--transparent': styleclass.transparent,
      'tui-iconBtn--stealth': styleclass.stealth,
      'tui-iconBtn--textFirst': styleclass.textFirst,
      'tui-iconBtn--toggle': styleclass.toggle,
      'tui-iconBtn--toolbar': styleclass.toolbar,
      'tui-iconBtn--selected': styleclass.selected,
      'tui-iconBtn--xsmall': styleclass.xsmall,
      'tui-iconBtn--transparent-noPadding': styleclass.transparentNoPadding,
    }"
    :disabled="disabled"
    :formaction="formaction"
    :formenctype="formenctype"
    :formmethod="formmethod"
    :formnovalidate="formnovalidate"
    :formtarget="formtarget"
    :name="name"
    :type="type"
    :value="value"
    @click="$emit('click', $event)"
  >
    <span class="tui-iconBtn__wrap">
      <span class="tui-iconBtn__label">
        <span class="tui-iconBtn__icon" aria-hidden="true">
          <slot />
        </span>
        <span v-if="text" class="tui-iconBtn__text">
          {{ text }}
        </span>
      </span>
      <Caret v-if="caret" class="tui-iconBtn__caret" />
    </span>
  </button>
</template>

<script>
import Caret from 'totara_core/components/decor/Caret';

export default {
  components: {
    Caret,
  },

  props: {
    ariaDescribedby: String,
    ariaExpanded: {
      type: [Boolean, String],
      default: false,
    },
    ariaLabel: {
      type: [Boolean, String],
      required: true,
    },
    autofocus: Boolean,
    caret: Boolean,
    styleclass: {
      default: () => ({
        primary: false,
        small: false,
        circle: false,
        square: false,
        toggle: false,
        transparent: false,
        textFirst: false,
        xsmall: false,
        transparentNoPadding: false,
      }),
      type: Object,
    },
    disabled: Boolean,
    formaction: String,
    formenctype: {
      type: String,
      validator(value) {
        const allowedOptions = [
          'application/x-www-form-urlencoded',
          'multipart/form-data',
          'text/plain',
        ];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    formmethod: {
      type: String,
      validator(value) {
        const allowedOptions = ['get', 'post'];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    formnovalidate: Boolean,
    formtarget: {
      type: String,
      validator(value) {
        const allowedOptions = ['_blank', '_parent', '_self', '_top'];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    name: String,
    text: String,
    type: {
      default: 'button',
      type: String,
      validator(value) {
        const allowedOptions = ['button', 'reset', 'submit'];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    value: String,
  },
};
</script>
