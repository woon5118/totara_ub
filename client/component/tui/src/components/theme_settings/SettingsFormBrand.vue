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
  @package tui
-->

<template>
  <Uniform
    v-if="initialValuesSet"
    :initial-values="initialValues"
    :errors="errorsForm"
    :validate="validate"
    @change="handleChange"
    @submit="submit"
  >
    <FormRowStack spacing="large">
      <FormRow
        :label="$str('formbrand_label_logo', 'totara_tui')"
        :is-stacked="true"
      >
        <ImageUploadSetting
          :metadata="fileData.sitelogo"
          :aria-describedby="$id('formbrand-logo-details')"
          :aria-label-extension="$str('formbrand_label_logo', 'totara_tui')"
          :context-id="contextId"
        />
        <FormRowDetails :id="$id('formbrand-logo-details')">
          {{ $str('formbrand_details_logo', 'totara_tui') }}
        </FormRowDetails>
      </FormRow>

      <FormRow
        :label="$str('formbrand_label_logoalttext', 'totara_tui')"
        :is-stacked="true"
      >
        <FormText
          :name="['formbrand_field_logoalttext', 'value']"
          :aria-describedby="$id('formbrand-logoalttext-details')"
          required
        />
        <FormRowDetails :id="$id('formbrand-logoalttext-details')">
          {{ $str('formbrand_details_logoalttext', 'totara_tui') }}
        </FormRowDetails>
      </FormRow>

      <FormRow
        :label="$str('formbrand_label_favicon', 'totara_tui')"
        :is-stacked="true"
      >
        <ImageUploadSetting
          :metadata="fileData.sitefavicon"
          :aria-describedby="$id('formbrand-favicon-details')"
          :aria-label-extension="$str('formbrand_label_favicon', 'totara_tui')"
          :context-id="contextId"
        />
        <FormRowDetails :id="$id('formbrand-favicon-details')">
          {{ $str('formbrand_details_favicon', 'totara_tui') }}
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
                $str('tabbrand', 'totara_tui') +
                  ' ' +
                  $str('settings', 'totara_core')
              )
            "
            :disabled="isSaving"
            type="submit"
          />
        </ButtonGroup>
      </FormRow>
    </FormRowStack>
  </Uniform>
</template>

<script>
import theme_settings from 'tui/lib/theme_settings';
import {
  Uniform,
  FormRow,
  FormRowStack,
  FormText,
} from 'tui/components/uniform';
import ImageUploadSetting from 'tui/components/theme_settings/ImageUploadSetting';
import FormRowDetails from 'tui/components/form/FormRowDetails';
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';

export default {
  components: {
    Uniform,
    FormRow,
    FormRowStack,
    FormRowDetails,
    FormText,
    ImageUploadSetting,
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
    // Array of Objects, each describing the properties for specifically file
    // upload fields that are part of this Form.
    fileFormFieldData: {
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
    // Context ID.
    contextId: [Number, String],
  },

  data() {
    return {
      initialValues: {
        formbrand_field_logoalttext: {
          value: null,
          type: 'text',
        },
      },
      fileData: {
        sitefavicon: null,
        sitelogo: null,
      },
      initialValuesSet: false,
      errorsForm: null,
      valuesForm: null,
      resultForm: null,
      theme_settings: theme_settings,
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
    let mergedFormData = this.theme_settings.mergeFormData(this.initialValues, [
      this.savedFormFieldData,
      this.valuesForm || [],
    ]);
    this.initialValues = this.theme_settings.getResolvedInitialValues(
      mergedFormData
    );

    // handle fileuploader setup independently of Uniform and initialValues
    // because file uploading doesn't really work in a way that Uniform can
    // fully support
    for (let i = 0; i < this.fileFormFieldData.length; i++) {
      let fileData = this.fileFormFieldData[i];
      if (typeof this.fileData[fileData.ui_key] !== 'undefined') {
        this.fileData[fileData.ui_key] = fileData;
      }
    }

    this.initialValuesSet = true;
  },

  methods: {
    validate() {
      const errors = {};
      return errors;
    },

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
        form: 'brand',
        fields: [],
        files: [],
      };

      // handle non-image upload form fields
      Object.keys(currentValues).forEach(field => {
        data.fields.push({
          name: field,
          type: currentValues[field].type,
          value: String(currentValues[field].value),
        });
      });

      // image upload form field data formatting as it is handled
      // differently to other form fields in our GraphQL mutation
      Object.keys(this.fileData).forEach(file => {
        if (this.fileData[file]) {
          data.files.push(this.fileData[file]);
        }
      });

      return data;
    },
  },
};
</script>

<lang-strings>
{
  "totara_tui": [
    "form_details_default",
    "formbrand_label_logo",
    "formbrand_details_logo",
    "formbrand_label_logoalttext",
    "formbrand_details_logoalttext",
    "formbrand_label_favicon",
    "formbrand_details_favicon",
    "tabbrand"
  ],
  "totara_core": [
    "save",
    "saveextended",
    "settings"
  ]
}
</lang-strings>
