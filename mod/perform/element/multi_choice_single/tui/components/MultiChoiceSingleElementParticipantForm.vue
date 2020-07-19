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
  @module performelement_multi_choice_single
-->
<template>
  <FormScope :path="path">
    <FormRadioGroup :validate="answerValidator" name="answer_option">
      <Radio
        v-for="item in element.data.options"
        :key="item.name"
        :value="item.name"
        >{{ item.value }}</Radio
      >
    </FormRadioGroup>
  </FormScope>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import Radio from 'totara_core/components/form/Radio';
import FormRadioGroup from 'totara_core/components/uniform/FormRadioGroup';

export default {
  components: {
    FormScope,
    Radio,
    FormRadioGroup,
  },

  props: {
    path: [String, Array],
    error: String,
    element: Object,
  },
  methods: {
    /**
     * answer validator based on element config
     *
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.element.is_required) {
        const isEmpty =
          !val || (typeof val === 'string' && val.trim().length === 0);
        if (isEmpty)
          return this.$str(
            'error_you_must_answer_this_question',
            'performelement_multi_choice_single'
          );
      }
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_multi_choice_single": [
      "error_you_must_answer_this_question"
    ]
  }
</lang-strings>
