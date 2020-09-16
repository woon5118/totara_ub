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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @module performelement_custom_rating_scale
-->
<template>
  <FormScope :path="path">
    <FormRadioGroup name="answer_option" :validate="answerValidator">
      <Radio
        v-for="(item, index) in element.data.options"
        :key="index"
        :value="item.name"
        >{{
          $str('answer_output', 'performelement_custom_rating_scale', {
            label: item.value.text,
            count: item.value.score,
          })
        }}</Radio
      >
    </FormRadioGroup>
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import Radio from 'tui/components/form/Radio';
import FormRadioGroup from 'tui/components/uniform/FormRadioGroup';

export default {
  components: {
    FormScope,
    Radio,
    FormRadioGroup,
  },
  props: {
    path: [String, Array],
    error: String,
    isDraft: Boolean,
    element: {
      type: Object,
      required: true,
    },
  },
  methods: {
    /**
     * answer validator based on element config
     *
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.isDraft) {
        return null;
      }

      if (this.element.is_required && !val) {
        return this.$str('required', 'performelement_custom_rating_scale');
      }
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_custom_rating_scale": [
      "answer_output",
      "required"
    ]
  }
</lang-strings>
