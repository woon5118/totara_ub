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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module performelement_numeric_rating_scale
-->

<template>
  <div class="tui-numericRatingScaleAdminEdit">
    <PerformAdminCustomElementEdit
      :initial-values="initialValues"
      :settings="settings"
      @cancel="$emit('display')"
      @change="updateRangeValues"
      @update="$emit('update', $event)"
    >
      <!-- Min value -->
      <FormRow
        :label="$str('low_value_label', 'performelement_numeric_rating_scale')"
        required
      >
        <FormNumber
          name="lowValue"
          :validations="v => [v.required(), v.number(), v.min(0)]"
          char-length="10"
        />
      </FormRow>

      <!-- Max value -->
      <FormRow
        :label="$str('high_value_label', 'performelement_numeric_rating_scale')"
        :helpmsg="
          $str('numeric_max_value_help', 'performelement_numeric_rating_scale')
        "
        required
      >
        <FormNumber
          name="highValue"
          :validations="v => [v.required(), v.number(), v.min(minValue)]"
          char-length="10"
        />
      </FormRow>

      <!-- Default value -->
      <FormRow
        :label="
          $str('default_number_label', 'performelement_numeric_rating_scale')
        "
        :helpmsg="
          $str('default_value_help_text', 'performelement_numeric_rating_scale')
        "
        required
      >
        <FormNumber
          name="defaultValue"
          :validations="
            v => [v.number(), v.required(), v.min(lowValue), v.max(highValue)]
          "
          char-length="10"
        />
      </FormRow>
    </PerformAdminCustomElementEdit>
  </div>
</template>

<script>
import { FormRow, FormNumber } from 'tui/components/uniform';
import PerformAdminCustomElementEdit from 'mod_perform/components/element/PerformAdminCustomElementEdit';

export default {
  components: {
    FormRow,
    FormNumber,
    PerformAdminCustomElementEdit,
  },

  inheritAttrs: false,

  props: {
    data: Object,
    identifier: String,
    rawTitle: String,
    settings: Object,
  },

  data() {
    return {
      initialValues: {
        defaultValue:
          this.data && this.data.defaultValue ? this.data.defaultValue : null,
        highValue:
          this.data && this.data.highValue ? this.data.highValue : null,
        identifier: this.identifier,
        lowValue: this.data && this.data.lowValue ? this.data.lowValue : null,
        rawTitle: this.rawTitle,
        responseRequired: true,
      },

      lowValue: this.data && this.data.lowValue ? this.data.lowValue : '0',
      highValue: this.data && this.data.highValue ? this.data.highValue : '0',
    };
  },

  computed: {
    minValue() {
      return this.lowValue ? Number(this.lowValue) + 2 : null;
    },
  },

  methods: {
    /**
     * Update range values based on user input for validation
     *
     * @param {Object} values
     */
    updateRangeValues(values) {
      this.lowValue = values.lowValue;
      this.highValue = values.highValue;
    },
  },
};
</script>
<lang-strings>
{
  "performelement_numeric_rating_scale": [
    "default_number_label",
    "default_value_help_text",
    "high_value_label",
    "low_value_label",
    "numeric_max_value_help",
    "scale_numeric_values"
  ]
}
</lang-strings>
