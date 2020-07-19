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
  @module totara_core
-->

<template>
  <textarea
    :id="id"
    class="tui-formTextarea"
    :aria-describedby="ariaDescribedby"
    :aria-label="ariaLabel"
    :aria-labelledby="ariaLabelledby"
    :autocomplete="autocomplete"
    :autofocus="autofocus"
    :cols="cols"
    :dir="dir"
    :disabled="disabled"
    :maxlength="maxlength"
    :minlength="minlength"
    :name="name"
    :placeholder="placeholder"
    :readonly="readonly"
    :required="required"
    :rows="rows"
    :spellcheck="spellcheck"
    :wrap="wrap"
    :value="value"
    @input="$emit('input', $event.target.value)"
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
    cols: Number,
    dir: {
      type: String,
      validator: x => ['auto', 'ltr', 'rtl', null].includes(x),
    },
    disabled: Boolean,
    id: String,
    maxlength: Number,
    minlength: Number,
    name: String,
    placeholder: String,
    readonly: Boolean,
    required: Boolean,
    rows: [Number, String],
    spellcheck: {
      type: [String, Boolean],
      default: null,
    },
    wrap: String,
    value: String,
  },
};
</script>
