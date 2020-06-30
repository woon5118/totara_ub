<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @package performelement_date_picker
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
import FormScope from 'totara_core/components/reform/FormScope';
import { FormDateSelector } from 'totara_core/components/uniform';

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
