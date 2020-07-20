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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_core
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
    :title="titleText"
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
import Caret from 'tui/components/decor/Caret';

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
    title: String,
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
  computed: {
    titleText() {
      if (this.title) return this.title;

      return this.text !== this.ariaLabel ? this.ariaLabel : false;
    },
  },
};
</script>
