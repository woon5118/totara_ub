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
      name="response"
      :validations="validations"
      :char-length="50"
    />
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import FormTextarea from 'tui/components/uniform/FormTextarea';
import { v as validation } from 'tui/validation';

export default {
  components: {
    FormScope,
    FormTextarea,
  },
  props: {
    path: {
      type: [String, Array],
      default: '',
    },
    error: String,
    isDraft: Boolean,
    element: Object,
  },
  computed: {
    /**
     * An array of validation rules for the element.
     * The rules returned depend on if we are saving as draft or if a response is required or not.
     *
     * @return {(function|object)[]}
     */
    validations() {
      if (!this.isDraft && this.element && this.element.is_required) {
        return [validation.required()];
      }

      return [];
    },
  },
  methods: {
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

      return value.response.trim();
    },
  },
};
</script>
