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
  @module totara_core
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
import Caret from 'tui/components/decor/Caret';

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
