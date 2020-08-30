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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package theme_ventura
-->

<template>
  <Uniform
    v-if="initialValuesSet"
    :initial-values="initialValues"
    :errors="errorsForm"
    @change="handleChange"
    @submit="submit"
  >
    <FormRow
      :label="$str('formcustom_label_customcss', 'theme_ventura')"
      :is-stacked="true"
    >
      <FormTextarea
        :name="['formcustom_field_customcss', 'value']"
        spellcheck="false"
        rows="6"
        char-length="full"
        :aria-describedby="$id('formcustom-customcss-details')"
      />
      <FormRowDetails :id="$id('formcustom-customcss-details')">
        {{ $str('formcustom_details_customcss', 'theme_ventura') }}
      </FormRowDetails>
    </FormRow>

    <FormRow>
      <ButtonGroup>
        <Button
          :styleclass="{ primary: 'true' }"
          :text="$str('save', 'totara_core')"
          :aria-label="
            $str(
              'saveextended',
              'totara_core',
              $str('tabcustom', 'theme_ventura') +
                ' ' +
                $str('settings', 'totara_core')
            )
          "
          :disabled="isSaving"
          type="submit"
        />
      </ButtonGroup>
    </FormRow>
  </Uniform>
</template>

<script>
import futils from 'theme_ventura/formutils';
import { Uniform, FormRow, FormTextarea } from 'tui/components/uniform';
import FormRowDetails from 'tui/components/form/FormRowDetails';
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';

export default {
  components: {
    Uniform,
    FormRow,
    FormRowDetails,
    FormTextarea,
    Button,
    ButtonGroup,
  },

  props: {
    // Array of Objects, each describing the properties for fields that are part
    // of this Form. There is only an Object present in this Array if it came
    // from the server as it was previously saved
    savedFormFieldData: {
      type: Array,
      default: function() {
        return [];
      },
    },
    // Saving state, controlled by parent component GraphQl mutation handling
    isSaving: {
      type: Boolean,
      default: function() {
        return false;
      },
    },
  },

  data() {
    return {
      initialValues: {
        formcustom_field_customcss: {
          value: '',
          type: 'text',
        },
      },
      initialValuesSet: false,
      formutils: futils,
      errorsForm: null,
      valuesForm: null,
      resultForm: null,
    };
  },

  /**
   * Prepare data for consumption within Uniform
   **/
  mounted() {
    // Set the data for this Form based on (in order):
    // - use previously saved Form data from GraphQL query
    // - missing field data then supplied by Theme JSON mapping data
    // - then locally held state until (takes precedence until page is reloaded)
    let mergedFormData = this.formutils.mergeFormData(this.initialValues, [
      this.savedFormFieldData,
      this.valuesForm || [],
    ]);
    this.initialValues = this.formutils.getResolvedInitialValues(
      mergedFormData
    );
    this.initialValuesSet = true;
  },

  methods: {
    handleChange(values) {
      this.valuesForm = values;
      if (this.errorsForm) {
        this.errorsForm = null;
      }
    },

    /**
     * Handle submission of an embedded form.
     *
     * @param {Object} currentValues The submitted form data.
     */
    submit(currentValues) {
      if (this.errorsForm) {
        this.errorsForm = null;
      }
      this.resultForm = currentValues;

      let dataToMutate = this.formatDataForMutation(currentValues);
      this.$emit('submit', dataToMutate);
    },

    /**
     * Takes Form field data and formats it to meet GraphQL mutation expectations
     *
     * @param {Object} currentValues The submitted form data.
     * @return {Object}
     **/
    formatDataForMutation(currentValues) {
      let data = {
        form: 'custom',
        fields: [],
      };

      Object.keys(currentValues).forEach(field => {
        data.fields.push({
          name: field,
          type: currentValues[field].type,
          value: String(currentValues[field].value),
        });
      });

      return data;
    },
  },
};
</script>

<lang-strings>
{
  "theme_ventura": [
    "formcustom_label_customcss",
    "formcustom_details_customcss",
    "tabcustom"
  ],
  "totara_core": [
    "save",
    "saveextended",
    "settings"
  ]
}
</lang-strings>
