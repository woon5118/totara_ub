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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module performelement_short_text
-->
<template>
  <FormScope :path="path" :process="process">
    <FormText
      name="answer_text"
      :validations="v => [answerRequired, maxLength]"
    />
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import { FormText } from 'tui/components/uniform';
import { v as validation } from 'tui/validation';

export default {
  components: {
    FormScope,
    FormText,
  },

  props: {
    path: [String, Array],
    error: String,
    element: Object,
  },
  methods: {
    process(value) {
      if (!value) {
        return { answer_text: '' };
      }

      value.answer_text = value.answer_text.trim();

      return value;
    },

    /**
     * answer validator based on element config
     *
     * @return {function[]}
     */
    answerRequired(val) {
      if (this.element.is_required) {
        const requiredValidation = validation.required();

        if (requiredValidation.validate(val)) {
          return null;
        }

        return this.$str(
          'error_you_must_answer_this_question',
          'performelement_short_text'
        );
      }
    },

    /**
     * Slightly tweaked maxLength validator to support the fact val may not be set at all.
     *
     * @param val
     * @return {string|null}
     */
    maxLength(val) {
      if (!val) {
        return null;
      }

      const maxLengthValidation = validation.maxLength(1024);

      if (maxLengthValidation.validate(val)) {
        return null;
      }

      return maxLengthValidation.message();
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_short_text": [
    "error_you_must_answer_this_question"
  ]
  }
</lang-strings>
