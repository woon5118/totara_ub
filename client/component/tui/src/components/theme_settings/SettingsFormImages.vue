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
    <Collapsible
      :label="$str('formimages_group_core', 'totara_tui')"
      :initial-state="true"
    >
      <FormRowStack>
        <FormRow
          :label="$str('formimages_label_displaylogin', 'totara_tui')"
          :is-stacked="true"
        >
          <FormCheckbox
            :name="['formimages_field_displaylogin', 'value']"
            :aria-describedby="$id('formimages-displaylogin-defaults')"
          />
          <FormRowDefaults :id="$id('formimages-displaylogin-defaults')">{{
            $str('enabled', 'totara_core')
          }}</FormRowDefaults>
        </FormRow>

        <FormRow
          :label="$str('formimages_label_login', 'totara_tui')"
          :is-stacked="true"
        >
          <ImageUploadSetting
            :metadata="fileData.sitelogin"
            :aria-label-extension="
              $str(
                'defaultimage',
                'totara_tui',
                $str('formimages_label_login', 'totara_tui')
              )
            "
            :aria-describedby="$id('formimages-login-details')"
          />
          <FormRowDetails :id="$id('formimages-login-details')">
            {{ $str('formimages_details_login', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>

        <FormRow
          :label="$str('formimages_label_loginalttext', 'totara_tui')"
          :is-stacked="true"
        >
          <FormText
            :name="['formimages_field_loginalttext', 'value']"
            :aria-describedby="$id('formimages-loginalttext-details')"
          />
          <FormRowDetails :id="$id('formimages-loginalttext-details')">
            {{ $str('formimages_details_loginalttext', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>
      </FormRowStack>
    </Collapsible>

    <Collapsible
      :label="$str('formimages_group_learn', 'totara_tui')"
      :initial-state="true"
    >
      <FormRowStack>
        <FormRow
          :label="$str('formimages_label_course', 'totara_tui')"
          :is-stacked="true"
        >
          <ImageUploadSetting
            :metadata="fileData.learncourse"
            :aria-label-extension="
              $str(
                'defaultimage',
                'totara_tui',
                $str('formimages_label_course', 'totara_tui')
              )
            "
            :aria-describedby="$id('formimages-course-details')"
          />
          <FormRowDetails :id="$id('formimages-course-details')">
            {{ $str('formimages_details_course', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>
        <FormRow
          :label="$str('formimages_label_program', 'totara_tui')"
          :is-stacked="true"
        >
          <ImageUploadSetting
            :metadata="fileData.learnprogram"
            :aria-label-extension="
              $str(
                'defaultimage',
                'totara_tui',
                $str('formimages_label_program', 'totara_tui')
              )
            "
            :aria-describedby="$id('formimages-program-details')"
          />
          <FormRowDetails :id="$id('formimages-program-details')">
            {{ $str('formimages_details_program', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>
        <FormRow
          :label="$str('formimages_label_cert', 'totara_tui')"
          :is-stacked="true"
        >
          <ImageUploadSetting
            :metadata="fileData.learncert"
            :aria-label-extension="
              $str(
                'defaultimage',
                'totara_tui',
                $str('formimages_label_cert', 'totara_tui')
              )
            "
            :aria-describedby="$id('formimages-cert-details')"
          />
          <FormRowDetails :id="$id('formimages-cert-details')">
            {{ $str('formimages_details_cert', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>
      </FormRowStack>
    </Collapsible>

    <Collapsible
      :label="$str('formimages_group_engage', 'totara_tui')"
      :initial-state="true"
    >
      <FormRowStack>
        <FormRow
          :label="$str('formimages_label_resource', 'totara_tui')"
          :is-stacked="true"
        >
          <ImageUploadSetting
            :metadata="fileData.engageresource"
            :aria-label-extension="
              $str(
                'defaultimage',
                'totara_tui',
                $str('formimages_label_resource', 'totara_tui')
              )
            "
            :aria-describedby="$id('formimages-resource-details')"
          />
          <FormRowDetails :id="$id('formimages-resource-details')">
            {{ $str('formimages_details_resource', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>
        <FormRow
          :label="$str('formimages_label_workspace', 'totara_tui')"
          :is-stacked="true"
        >
          <ImageUploadSetting
            :metadata="fileData.engageworkspace"
            :aria-label-extension="
              $str(
                'defaultimage',
                'totara_tui',
                $str('formimages_label_workspace', 'totara_tui')
              )
            "
            :aria-describedby="$id('formimages-workspace-details')"
          />
          <FormRowDetails :id="$id('formimages-workspace-details')">
            {{ $str('formimages_details_workspace', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>
      </FormRowStack>
    </Collapsible>

    <FormRow>
      <ButtonGroup>
        <Button
          :styleclass="{ primary: 'true' }"
          :text="$str('save', 'totara_core')"
          :aria-label="
            $str(
              'saveextended',
              'totara_core',
              $str('tabimages', 'totara_tui') +
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
import theme_settings from 'tui/lib/theme_settings';
import Collapsible from 'tui/components/collapsible/Collapsible';
import {
  Uniform,
  FormRow,
  FormCheckbox,
  FormText,
} from 'tui/components/uniform';
import ImageUploadSetting from 'tui/components/theme_settings/ImageUploadSetting';
import FormRowDetails from 'tui/components/form/FormRowDetails';
import FormRowDefaults from 'tui/components/form/FormRowDefaults';
import FormRowStack from 'tui/components/form/FormRowStack';
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';

export default {
  components: {
    Collapsible,
    Uniform,
    FormRow,
    FormRowDetails,
    FormRowDefaults,
    FormCheckbox,
    FormText,
    ImageUploadSetting,
    FormRowStack,
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
    // Array of Objects, each describing the properties required to send back
    // file data for storage
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
  },

  data() {
    return {
      initialValues: {
        formimages_field_displaylogin: {
          value: true,
          type: 'boolean',
        },
        formimages_field_loginalttext: {
          value: '',
          type: 'text',
        },
      },
      fileData: {
        sitelogin: null,
        learncourse: null,
        learnprogram: null,
        learncert: null,
        engageresource: null,
        engageworkspace: null,
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

    // handle fileuploader setup
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
        form: 'images',
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
    "defaultimage",
    "form_details_default",
    "formimages_group_core",
    "formimages_label_displaylogin",
    "formimages_label_login",
    "formimages_details_login",
    "formimages_label_loginalttext",
    "formimages_details_loginalttext",
    "formimages_group_learn",
    "formimages_label_course",
    "formimages_details_course",
    "formimages_label_program",
    "formimages_details_program",
    "formimages_label_cert",
    "formimages_details_cert",
    "formimages_group_engage",
    "formimages_label_resource",
    "formimages_details_resource",
    "formimages_label_workspace",
    "formimages_details_workspace",
    "tabimages"
  ],
  "totara_core": [
    "save",
    "saveextended",
    "settings",
    "enabled"
  ]
}
</lang-strings>
