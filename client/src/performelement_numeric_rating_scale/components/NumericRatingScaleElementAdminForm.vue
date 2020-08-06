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
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <div class="tui-elementEditNumericRatingScale">
        <Uniform
          v-slot="{ getSubmitting }"
          :initial-values="formValues"
          :vertical="false"
          validation-mode="submit"
          input-width="full"
          @change="formValues = $event"
          @submit="handleSubmit"
        >
          <!-- Question -->
          <FormRow
            :label="
              $str('question_label', 'performelement_numeric_rating_scale')
            "
            required
          >
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
              :placeholder="
                $str(
                  'question_placeholder',
                  'performelement_numeric_rating_scale'
                )
              "
            />
          </FormRow>

          <!-- Scale numeric values -->
          <FormRow
            :label="
              $str(
                'scale_numeric_values',
                'performelement_numeric_rating_scale'
              )
            "
            :helpmsg="numericValuesHelp"
            required
          >
            <div class="tui-elementEditNumericRatingScale__values">
              <FormNumber
                name="lowValue"
                :aria-label="lowValueLabel"
                :validations="lowValueValidations"
              />
              <FormNumber
                name="highValue"
                :aria-label="highValueLabel"
                :validations="highValueValidations"
              />
            </div>
          </FormRow>

          <!-- Preview -->
          <FormRow
            :label="$str('preview', 'performelement_numeric_rating_scale')"
            :helpmsg="previewHelp"
          >
            <Range
              name="preview"
              :disabled="true"
              :value="null"
              :default-value="formValues.defaultValue"
              :show-labels="false"
              :min="formValues.lowValue"
              :max="formValues.highValue"
            />
          </FormRow>

          <!-- Default value -->
          <FormRow
            :label="
              $str(
                'default_number_label',
                'performelement_numeric_rating_scale'
              )
            "
            :helpmsg="defaultValueHelp"
            required
          >
            <div class="tui-elementEditNumericRatingScale__values">
              <FormNumber
                name="defaultValue"
                :validations="
                  v => [
                    v.number(),
                    v.required(),
                    between(formValues.lowValue, formValues.highValue),
                  ]
                "
              />
            </div>
          </FormRow>

          <!-- Reporting ID -->
          <IdentifierInput />

          <!-- Response required -->
          <FormRow
            :label="$str('section_element_response_required', 'mod_perform')"
            :helpmsg="responseRequiredHelp"
          >
            <FormCheckbox name="responseRequired" :disabled="true" />
          </FormRow>

          <!-- Actions -->
          <FormRow>
            <div class="tui-elementEditShortText__action-buttons">
              <FormActionButtons
                :submitting="getSubmitting()"
                @cancel="cancel"
              />
            </div>
          </FormRow>
        </Uniform>
      </div>
    </template>
  </ElementAdminForm>
</template>

<script>
import {
  Uniform,
  FormRow,
  FormText,
  FormNumber,
  FormCheckbox,
} from 'tui/components/uniform';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import IdentifierInput from 'mod_perform/components/element/admin_form/IdentifierInput';
import Range from 'tui/components/form/Range';

export default {
  components: {
    ElementAdminForm,
    Uniform,
    FormRow,
    FormText,
    FormNumber,
    FormActionButtons,
    Range,
    FormCheckbox,
    IdentifierInput,
  },

  mixins: [AdminFormMixin],

  props: {
    type: Object,
    title: String,
    rawTitle: String,
    identifier: String,
    isRequired: {
      type: Boolean,
      default: false,
    },
    activityState: {
      type: Object,
      required: true,
    },
    data: Object,
    error: String,
  },

  data() {
    return {
      formValues: {
        title: this.title,
        rawTitle: this.rawTitle,
        identifier: this.identifier,
        lowValue: this.data ? this.data.lowValue || 0 : 0,
        highValue: this.data ? this.data.highValue || 0 : 0,
        defaultValue: this.data ? this.data.defaultValue || 0 : 0,
        responseRequired: true, // Always required.
      },
      numericValuesHelp: this.$str(
        'numeric_values_help',
        'performelement_numeric_rating_scale'
      ),
      previewHelp: this.$str(
        'preview_help',
        'performelement_numeric_rating_scale'
      ),
      defaultValueHelp: this.$str(
        'default_value_help',
        'performelement_numeric_rating_scale'
      ),
      responseRequiredHelp: this.$str(
        'response_required_help',
        'performelement_numeric_rating_scale'
      ),
      lowValueLabel: this.$str(
        'low_value_label',
        'performelement_numeric_rating_scale'
      ),
      highValueLabel: this.$str(
        'high_value_label',
        'performelement_numeric_rating_scale'
      ),
    };
  },

  computed: {
    maxValue() {
      return this.formValues.highValue
        ? Number(this.formValues.highValue) - 2
        : null;
    },

    minValue() {
      return this.formValues.lowValue
        ? Number(this.formValues.lowValue) + 2
        : null;
    },

    defaultValue() {
      const low = Number(this.formValues.lowValue);
      const high = Number(this.formValues.highValue);
      return Math.ceil((high - low) / 2) + low;
    },
  },

  watch: {
    defaultValue(value) {
      if (value) {
        this.formValues.defaultValue = value;
      }
    },
  },

  methods: {
    handleSubmit(values) {
      this.$emit('update', {
        title: values.rawTitle,
        identifier: values.identifier,
        is_required: values.responseRequired,
        data: values,
      });
    },

    cancel() {
      this.$emit('display');
    },

    lowValueValidations(v) {
      const maxValue = this.maxValue ? [this.max(this.maxValue)] : [];
      return [v.required(), v.number()].concat(maxValue);
    },

    highValueValidations(v) {
      const minValue = this.minValue ? [this.min(this.minValue)] : [];
      return [v.required(), v.number()].concat(minValue);
    },

    min(min) {
      return {
        validate: val => Number(val) >= min,
        message: () => `Value must be at least 2 more than low value`,
      };
    },

    max(max) {
      return {
        validate: val => Number(val) <= max,
        message: () => `Value must be at least 2 less than high value`,
      };
    },

    between(min, max) {
      return {
        validate: val => Number(val) >= min && Number(val) <= max,
        message: () => 'Value must be between low and high values inclusive',
      };
    },
  },
};
</script>
<lang-strings>
{
  "performelement_numeric_rating_scale": [
    "default_number_label",
    "default_value_help",
    "high_value_label",
    "low_value_label",
    "error:question_required",
    "error:question_length_exceeded",
    "numeric_values_help",
    "preview",
    "preview_help",
    "question_label",
    "question_placeholder",
    "response_required_help",
    "scale_numeric_values"
  ],
  "mod_perform": [
    "section_element_response_required",
    "reporting_identifier"
  ]
}
</lang-strings>
