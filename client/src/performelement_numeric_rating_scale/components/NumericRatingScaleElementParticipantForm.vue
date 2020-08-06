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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @package performelement_numeric_rating_scale
-->

<template>
  <FormScope :path="path">
    <div class="tui-elementEditNumericRatingScaleParticipantForm">
      <div class="tui-elementEditNumericRatingScaleParticipantForm__input">
        <FormRow>
          <FormNumber
            name="answer_value"
            :min="min"
            :max="max"
            :validations="v => [v.min(min), v.max(max)]"
          />
        </FormRow>
      </div>
      <FormRow>
        <FormRange
          name="answer_value"
          :default-value="element.data.defaultValue"
          :show-labels="false"
          :min="min"
          :max="max"
          :validations="rangeValidations"
        />
      </FormRow>
    </div>
  </FormScope>
</template>

<script>
import FormScope from 'tui/components/reform/FormScope';
import { FormRow, FormRange, FormNumber } from 'tui/components/uniform';

export default {
  components: {
    FormScope,
    FormRow,
    FormRange,
    FormNumber,
  },

  props: {
    path: [String, Array],
    error: String,
    element: {
      type: Object,
      required: true,
      validator: val => {
        if (['data'].indexOf(val) !== -1) {
          return ['lowValue', 'highValue'].indexOf(val.data) !== -1;
        }
        return false;
      },
    },
  },

  computed: {
    min() {
      return parseInt(this.element.data.lowValue, 10);
    },
    max() {
      return parseInt(this.element.data.highValue, 10);
    },
  },

  methods: {
    rangeValidations(v) {
      return this.element.is_required ? [v.required()] : [];
    },
  },
};
</script>
