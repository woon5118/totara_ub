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
  @module performelement_date_picker
-->
<template>
  <FormScope :path="path">
    <div>
      <FormDateSelector
        v-modal="dateValue"
        name="date"
        :years-midrange="midrangeYear"
        :years-before-midrange="midrangeYearBefore"
        :years-after-midrange="midrangeYearAfter"
        :validate="answerValidator"
      />
      <FormRowDetails>{{
        $str('date_picker_placeholder', 'performelement_date_picker')
      }}</FormRowDetails>
    </div>
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import { FormDateSelector } from 'tui/components/uniform';
import FormRowDetails from 'tui/components/form/FormRowDetails';

export default {
  components: {
    FormScope,
    FormDateSelector,
    FormRowDetails,
  },

  props: {
    path: [String, Array],
    element: Object,
    error: String,
  },
  data() {
    return {
      dateValue: {},
      disabled: false,
      errors: null,
      midrangeYear: 2000,
      midrangeYearBefore: 100,
      midrangeYearAfter: 50,
      selectedDate: {},
    };
  },

  methods: {
    /**
     * answer validator
     *
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.element.is_required) {
        if (!val || typeof val === 'undefined')
          return this.$str(
            'error_you_must_answer_this_question',
            'performelement_date_picker'
          );
      }
      if (typeof val === 'undefined') {
        return this.$str('error_invalid_date', 'performelement_date_picker');
      }
    },
    submit(values) {
      if (values.date) {
        this.selectedDate = values.date;
      }
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_date_picker": [
        "error_invalid_date",
        "error_you_must_answer_this_question",
        "date_picker_placeholder"
    ]
  }
</lang-strings>
