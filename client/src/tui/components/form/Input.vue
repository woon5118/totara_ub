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
  @module tui
-->

<template>
  <input
    :id="id"
    class="tui-formInput"
    :aria-describedby="ariaDescribedby"
    :aria-invalid="ariaInvalid"
    :aria-label="ariaLabel"
    :aria-labelledby="ariaLabelledby"
    :autocomplete="autocomplete"
    :autofocus="autofocus"
    :class="{
      'tui-formInput--preIcon': styleclass.preIcon,
      'tui-formInput--transparent': styleclass.transparent,
    }"
    :dir="dir"
    :disabled="disabled"
    :list="list"
    :max="max"
    :maxlength="maxlength"
    :min="min"
    :minlength="minlength"
    :multiple="multiple"
    :name="name"
    :pattern="pattern"
    :placeholder="placeholder"
    :readonly="readonly"
    :required="required"
    :size="size"
    :spellcheck="spellcheck"
    :step="step"
    :type="type"
    :value="value"
    @input="$emit('input', $event.target.value)"
    @blur="$emit('blur')"
    @keydown.enter="$emit('submit', $event.target.value)"
  />
</template>

<script>
export default {
  props: {
    ariaDescribedby: [String, Boolean],
    ariaInvalid: [String, Boolean],
    ariaLabel: [String, Boolean],
    ariaLabelledby: [String, Boolean],
    autocomplete: String,
    autofocus: Boolean,
    dir: {
      type: String,
      validator: x => ['auto', 'ltr', 'rtl', null].includes(x),
    },
    disabled: Boolean,
    id: String,
    list: String,
    max: Number,
    maxlength: Number,
    min: Number,
    minlength: Number,
    multiple: String,
    name: String,
    pattern: String,
    placeholder: String,
    readonly: Boolean,
    required: Boolean,
    size: Number,
    spellcheck: Boolean,
    step: Number,
    styleclass: {
      default: () => ({
        preIcon: false,
        transparent: false,
      }),
      type: Object,
    },
    type: {
      required: true,
      type: String,
      validator: function(value) {
        const allowedOptions = [
          'email',
          'hidden',
          'number',
          'password',
          'search',
          'tel',
          'text',
          'time',
          'url',
          'week',
          'color',
        ];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    value: [Number, String],
  },
};
</script>
