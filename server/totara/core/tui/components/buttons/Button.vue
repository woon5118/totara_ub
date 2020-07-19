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
  @package totara_core
-->

<template>
  <button
    class="tui-formBtn"
    :aria-expanded="ariaExpanded"
    :aria-disabled="ariaDisabled"
    :aria-describedby="ariaDescribedby"
    :autofocus="autofocus"
    :class="{
      'tui-formBtn--alert': styleclass.alert,
      'tui-formBtn--prim': styleclass.primary,
      'tui-formBtn--small': styleclass.small,
      'tui-formBtn--srOnly': styleclass.srOnly,
      'tui-formBtn--transparent': styleclass.transparent,
      'tui-formBtn--reveal': styleclass.reveal,
      'tui-formBtn--stealth': styleclass.stealth,
      'tui-formBtn--toolbar': styleclass.toolbar,
      'tui-formBtn--selected': styleclass.selected,
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
    {{ text }}
    <Caret v-if="caret" class="tui-formBtn__caret" />
  </button>
</template>

<script>
import Caret from 'totara_core/components/decor/Caret';

export default {
  components: {
    Caret,
  },

  props: {
    ariaDisabled: [Boolean, String],
    ariaDescribedby: String,
    ariaExpanded: {
      type: [Boolean, String],
      default: false,
    },
    autofocus: Boolean,
    caret: Boolean,
    styleclass: {
      default: () => ({
        alert: false,
        primary: false,
        small: false,
        transparent: false,
      }),
      type: Object,
    },
    disabled: Boolean,
    formaction: String,
    formenctype: {
      type: String,
      validator: x =>
        [
          'application/x-www-form-urlencoded',
          'multipart/form-data',
          'text/plain',
        ].includes(x),
    },
    formmethod: {
      type: String,
      validator: x => ['get', 'post'].includes(x),
    },
    formnovalidate: Boolean,
    formtarget: {
      type: String,
      validator: x => ['_blank', '_parent', '_self', '_top'].includes(x),
    },
    name: String,
    text: {
      required: true,
      type: String,
    },
    type: {
      default: 'button',
      type: String,
      validator: x => ['button', 'reset', 'submit'].includes(x),
    },
    value: String,
  },
};
</script>
