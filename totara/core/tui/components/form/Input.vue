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
        ];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    value: [Number, String],
  },
};
</script>
