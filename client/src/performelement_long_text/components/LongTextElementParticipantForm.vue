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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @module performelement_long_text
-->
<template>
  <FormScope :path="path" :process="process">
    <FormTextarea
      :rows="6"
      name="answer_text"
      :validations="v => [answerRequired]"
    />
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import FormTextarea from 'tui/components/uniform/FormTextarea';

export default {
  components: {
    FormScope,
    FormTextarea,
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
        const isEmpty =
          !val || (typeof val === 'string' && val.trim().length === 0);
        if (isEmpty) {
          return this.$str(
            'error_you_must_answer_this_question',
            'performelement_long_text'
          );
        }
      }
    },
  },
};
</script>

<lang-strings>
  {
    "performelement_long_text": [
      "error_you_must_answer_this_question"
    ]
  }
</lang-strings>
