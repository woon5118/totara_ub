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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @module performelement_date_picker
-->
<template>
  <FormScope :path="path">
    <FormDateSelector
      v-modal="dateValue"
      name="date"
      :initial-current-date="false"
      :initial-custom-date="customDate"
      :type="isoType"
      :years-midrange="parseInt(midrangeYear)"
      :years-before-midrange="parseInt(midrangeYearBefore)"
      :years-after-midrange="parseInt(midrangeYearAfter)"
      :validate="answerValidator"
    />
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import { FormDateSelector } from 'tui/components/uniform';

export default {
  components: {
    FormScope,
    FormDateSelector,
  },

  props: {
    path: [String, Array],
    element: Object,
    error: String,
  },
  data() {
    return {
      currentDate: true,
      customDate: false,
      isoType: 'date',
      dateValue: {},
      disabled: false,
      errors: null,
      midrangeYear: 2000,
      midrangeYearBefore: 100,
      midrangeYearAfter: 50,
      selectedDate: {},
      timezoned: true,
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
        "error_you_must_answer_this_question"
    ]
  }
</lang-strings>
