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
        v-if="logoEditable"
        :label="$str('formbrand_label_logo', 'totara_tui')"
        :is-stacked="true"
      >
        <ImageUploadSetting
          :key="key"
          :metadata="fileData.sitelogo"
          :aria-describedby="$id('formbrand-logo-details')"
          :aria-label-extension="$str('formbrand_label_logo', 'totara_tui')"
          :context-id="contextId"
          :show-delete="showDelete(fileData.sitelogo)"
          @update="saveImage"
          @delete="resetImage"
        />
        <FormRowDetails :id="$id('formbrand-logo-details')">
          {{ $str('formbrand_details_logo', 'totara_tui') }}
        </FormRowDetails>
      </FormRow>

      <!-- Not allowing alt text change if image can't be changed -->
      <FormRow
        v-if="logoEditable"
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
        v-if="faviconEditable"
        :label="$str('formbrand_label_favicon', 'totara_tui')"
        :is-stacked="true"
      >
        <ImageUploadSetting
          :key="key"
          :metadata="fileData.sitefavicon"
          :aria-describedby="$id('formbrand-favicon-details')"
          :aria-label-extension="$str('formbrand_label_favicon', 'totara_tui')"
          :context-id="contextId"
          :show-delete="showDelete(fileData.sitefavicon)"
          @update="saveImage"
          @delete="resetImage"
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

// Mixins
import FileMixin from 'tui/mixins/settings_form_file_mixin';

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

  mixins: [FileMixin],

  props: {
    /**
     * Array of Objects, each describing the properties for fields that are part
     * of this Form. There is only an Object present in this Array if it came
     * from the server as it was previously saved
     */
    savedFormFieldData: {
      type: Array,
      default: function() {
        return [];
      },
    },

    /**
     *  Saving state, controlled by parent component GraphQl mutation handling
     */
    isSaving: {
      type: Boolean,
      default: function() {
        return false;
      },
    },

    /**
     *  Context ID.
     */
    contextId: [Number, String],

    /**
     * Tenant ID or null if global/multi-tenancy not enabled.
     */
    selectedTenantId: Number,

    /**
     *  Customizable tenant settings
     */
    customizableTenantSettings: {
      type: [Array, String],
      required: false,
    },
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

  computed: {
    logoEditable() {
      return this.canEditImage('sitelogo');
    },
    faviconEditable() {
      return this.canEditImage('sitefavicon');
    },
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
    this.initialValuesSet = true;
    this.$emit('mounted', {
      category: 'brand',
      values: this.formatDataForMutation(this.initialValues),
    });
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
     * Check whether the specific image can be customized
     * @param {String} key
     * @return {Boolean}
     */
    canEditImage(key) {
      if (!this.selectedTenantId) {
        return true;
      }

      if (!this.customizableTenantSettings) {
        return false;
      }

      if (Array.isArray(this.customizableTenantSettings)) {
        return this.customizableTenantSettings.includes(key);
      }

      return this.customizableTenantSettings === '*';
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
