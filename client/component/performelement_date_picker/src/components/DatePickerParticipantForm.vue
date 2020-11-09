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
  <FormScope :path="path" :process="process">
    <FormDateSelector
      name="response"
      :years-midrange="midrangeYear"
      :years-before-midrange="midrangeYearBefore"
      :years-after-midrange="midrangeYearAfter"
      :validations="validations"
    />
  </FormScope>
</template>

<script>
import { FormDateSelector, FormScope } from 'tui/components/uniform';
import { v as validation } from 'tui/validation';

export default {
  components: {
    FormScope,
    FormDateSelector,
  },

  props: {
    path: [String, Array],
    element: Object,
    isDraft: Boolean,
    error: String,
  },
  data() {
    return {
      midrangeYear: 2000,
      midrangeYearBefore: 100,
      midrangeYearAfter: 50,
    };
  },
  computed: {
    /**
     * An array of validation rules for the element.
     * The rules returned depend on if we are saving as draft or if a response is required or not.
     *
     * @return {(function|object)[]}
     */
    validations() {
      const rules = [validation.date(), this.fullDateRequired];

      if (this.isDraft) {
        return rules;
      }

      if (this.element && this.element.is_required) {
        // Required will also fail for haf filled in dates,
        // so we put it at the end so 'fullDateRequired' can be triggered first.
        return [...rules, validation.required()];
      }

      return rules;
    },
  },
  methods: {
    /**
     * Validation method, that requires the entire date to be filled in.
     *
     * @param value
     * @return {*}
     */
    fullDateRequired(value) {
      // Specifically we must test for undefined, null means not filled at all.
      if (typeof value === 'undefined') {
        return this.$str('error_invalid_date', 'performelement_date_picker');
      }
    },
    /**
     * Process the form values.
     *
     * @param value
     * @return {null|string}
     */
    process(value) {
      if (!value || !value.response) {
        return null;
      }

      return value.response;
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_date_picker": [
        "error_invalid_date"
    ]
  }
</lang-strings>
