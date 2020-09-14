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
    <FormRow
      :label="$str('formcolours_label_primary', 'totara_tui')"
      :is-stacked="true"
    >
      <FormColor
        :name="['color-state', 'value']"
        :validations="v => [v.required(), v.colorValueHex()]"
        :aria-describedby="
          $id('formcolours-primary-details') +
            ' ' +
            $id('formcolours-primary-defaults')
        "
      />
      <FormRowDefaults :id="$id('formcolours-primary-defaults')">
        {{
          theme_settings.getCSSVarDefault(
            mergedProcessedCssVariableData,
            'color-state'
          )
        }}
      </FormRowDefaults>
      <FormRowDetails :id="$id('formcolours-primary-details')">
        {{ $str('formcolours_details_primary', 'totara_tui') }}
      </FormRowDetails>
    </FormRow>

    <FormRow
      :label="$str('formcolours_label_useoverrides', 'totara_tui')"
      :is-stacked="true"
    >
      <FormToggleSwitch
        :aria-label="$str('formcolours_label_useoverrides', 'totara_tui')"
        :name="['formcolours_field_useoverrides', 'value']"
        :toggle-first="true"
        :aria-describedby="$id('formcolours-useoverrides-details')"
      />
      <FormRowDetails :id="$id('formcolours-useoverrides-details')">
        {{ $str('formcolours_details_useoverrides', 'totara_tui') }}
      </FormRowDetails>
    </FormRow>

    <FormFieldset v-if="colourOverridesEnabled">
      <FormRowStack>
        <FormRow
          :label="$str('formcolours_label_primarybuttons', 'totara_tui')"
          :is-stacked="true"
        >
          <FormColor
            :name="['btn-prim-accent-color', 'value']"
            :validations="v => [v.required(), v.colorValueHex()]"
            :aria-describedby="
              $id('formcolours-primarybuttons-details') +
                ' ' +
                $id('formcolours-primarybuttons-defaults')
            "
          />
          <FormRowDefaults :id="$id('formcolours-primarybuttons-defaults')">
            {{
              theme_settings.getCSSVarDefault(
                mergedProcessedCssVariableData,
                'btn-prim-accent-color'
              )
            }}
          </FormRowDefaults>
          <FormRowDetails :id="$id('formcolours-primarybuttons-details')">
            {{ $str('formcolours_details_primarybuttons', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>

        <FormRow
          :label="$str('formcolours_label_secondarybuttons', 'totara_tui')"
          :is-stacked="true"
        >
          <FormColor
            :name="['btn-accent-color', 'value']"
            :validations="v => [v.required(), v.colorValueHex()]"
            :aria-describedby="
              $id('formcolours-secondarybuttons-details') +
                ' ' +
                $id('formcolours-secondarybuttons-defaults')
            "
          />
          <FormRowDefaults :id="$id('formcolours-secondarybuttons-defaults')">
            {{
              theme_settings.getCSSVarDefault(
                mergedProcessedCssVariableData,
                'btn-accent-color'
              )
            }}
          </FormRowDefaults>
          <FormRowDetails :id="$id('formcolours-secondarybuttons-details')">
            {{ $str('formcolours_details_secondarybuttons', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>

        <FormRow
          :label="$str('formcolours_label_links', 'totara_tui')"
          :is-stacked="true"
        >
          <FormColor
            :name="['link-color', 'value']"
            :validations="v => [v.required(), v.colorValueHex()]"
            :aria-describedby="
              $id('formcolours-links-details') +
                ' ' +
                $id('formcolours-links-defaults')
            "
          />
          <FormRowDefaults :id="$id('formcolours-links-defaults')">
            {{
              theme_settings.getCSSVarDefault(
                mergedProcessedCssVariableData,
                'link-color'
              )
            }}
          </FormRowDefaults>
          <FormRowDetails :id="$id('formcolours-links-details')">
            {{ $str('formcolours_details_links', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>

        <Separator />
      </FormRowStack>
    </FormFieldset>

    <FormRowStack>
      <FormRow
        :label="$str('formcolours_label_accent', 'totara_tui')"
        :is-stacked="true"
      >
        <FormColor
          :name="['color-primary', 'value']"
          :validations="v => [v.required(), v.colorValueHex()]"
          :aria-describedby="
            $id('formcolours-accent-details') +
              ' ' +
              $id('formcolours-accent-defaults')
          "
        />
        <FormRowDefaults :id="$id('formcolours-accent-defaults')">
          {{
            theme_settings.getCSSVarDefault(
              mergedProcessedCssVariableData,
              'color-primary'
            )
          }}
        </FormRowDefaults>
        <FormRowDetails :id="$id('formcolours-accent-details')">
          {{ $str('formcolours_details_accent', 'totara_tui') }}
        </FormRowDetails>
      </FormRow>
    </FormRowStack>

    <Collapsible :label="$str('formcolours_moresettings', 'totara_tui')">
      <FormRowStack>
        <FormRow
          :label="$str('formcolours_label_headerbg', 'totara_tui')"
          :is-stacked="true"
        >
          <FormColor
            :name="['nav-bg-color', 'value']"
            :validations="v => [v.required(), v.colorValueHex()]"
            :aria-describedby="
              $id('formcolours-headerbg-details') +
                ' ' +
                $id('formcolours-headerbg-defaults')
            "
          />
          <FormRowDefaults :id="$id('formcolours-headerbg-defaults')">
            {{
              theme_settings.getCSSVarDefault(
                mergedProcessedCssVariableData,
                'nav-bg-color'
              )
            }}
          </FormRowDefaults>
          <FormRowDetails :id="$id('formcolours-headerbg-details')">
            {{ $str('formcolours_details_headerbg', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>

        <FormRow
          :label="$str('formcolours_label_headertext', 'totara_tui')"
          :is-stacked="true"
        >
          <FormColor
            :name="['nav-text-color', 'value']"
            :validations="v => [v.required(), v.colorValueHex()]"
            :aria-describedby="
              $id('formcolours-headertext-details') +
                ' ' +
                $id('formcolours-headertext-defaults')
            "
          />
          <FormRowDefaults :id="$id('formcolours-headertext-defaults')">
            {{
              theme_settings.getCSSVarDefault(
                mergedProcessedCssVariableData,
                'nav-text-color'
              )
            }}
          </FormRowDefaults>
          <FormRowDetails :id="$id('formcolours-headertext-details')">
            {{ $str('formcolours_details_headertext', 'totara_tui') }}
          </FormRowDetails>
        </FormRow>

        <FormRow
          :label="$str('formcolours_label_pagetext', 'totara_tui')"
          :is-stacked="true"
        >
          <FormColor
            :name="['color-text', 'value']"
            :validations="v => [v.required(), v.colorValueHex()]"
            :aria-describedby="
              $id('formcolours-pagetext-details') +
                ' ' +
                $id('formcolours-pagetext-defaults')
            "
          />
          <FormRowDefaults :id="$id('formcolours-pagetext-defaults')">
            {{
              theme_settings.getCSSVarDefault(
                mergedProcessedCssVariableData,
                'color-text'
              )
            }}
          </FormRowDefaults>
          <FormRowDetails :id="$id('formcolours-pagetext-details')">
            {{ $str('formcolours_details_pagetext', 'totara_tui') }}
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
              $str('tabcolours', 'totara_tui') +
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
import Separator from 'tui/components/decor/Separator';
import {
  Uniform,
  FormRow,
  FormColor,
  FormToggleSwitch,
} from 'tui/components/uniform';
import FormFieldset from 'tui/components/form/Fieldset';
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
    FormFieldset,
    FormRowDetails,
    FormRowDefaults,
    FormColor,
    FormToggleSwitch,
    FormRowStack,
    Separator,
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
    // Array of Objects, each describing the properties for fields that are part
    // of this Form. There is only an Object present in this Array if it was
    // present in Theme JSON data mapping (not GraphQL query), and the values
    // within each Object are defaults, not previously saved data.
    mergedDefaultCssVariableData: {
      type: Object,
      default: function() {
        return {};
      },
    },
    // Array of Objects, each describing the properties for fields that are part
    // of this Form. There is only an Object present in this Array if it was
    // present in Theme JSON data mapping (not GraphQL query), and the values
    // within each Object have processed/resolved values.
    mergedProcessedCssVariableData: {
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
        // mixed convention because some of these are derived from CSS var names
        formcolours_field_useoverrides: {
          value: false,
          type: 'boolean',
        },
        'nav-bg-color': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'nav-text-color': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'color-primary': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'btn-prim-accent-color': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'btn-accent-color': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'link-color': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'color-state': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
        'color-text': {
          value: null,
          type: null, // supplied by Theme-based JSON metadata
        },
      },
      initialValuesSet: false,
      colourOverridesEnabled: false,
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
    // - then locally held state (takes precedence until page is reloaded)
    let mergedFormData = this.theme_settings.mergeFormData(this.initialValues, [
      this.mergedProcessedCssVariableData,
      this.savedFormFieldData,
      this.valuesForm || [],
    ]);

    this.initialValues = this.theme_settings.getResolvedInitialValues(
      mergedFormData
    );

    // reactive data hook for override toggle
    this.colourOverridesEnabled = this.initialValues.formcolours_field_useoverrides.value;

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

      // update form-based reactive toggles
      if (typeof values.formcolours_field_useoverrides !== 'undefined') {
        this.colourOverridesEnabled =
          values.formcolours_field_useoverrides.value;
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
        form: 'colours',
        fields: null,
      };

      // save form field values that are non-default values
      let nonDefaultFields = [];
      Object.keys(currentValues).forEach(fieldName => {
        let currentField = currentValues[fieldName];

        // push all non-default valued CSS fields in
        if (currentField.type === 'value') {
          for (let i = 0; i < this.mergedProcessedCssVariableData.length; i++) {
            let cssDefault = this.mergedProcessedCssVariableData[i];

            if (
              fieldName === cssDefault.name &&
              currentField.value !== cssDefault.default
            ) {
              console.log(
                `Non-default value found for currentField: ${fieldName}`
              );
              nonDefaultFields.push({
                name: cssDefault.name,
                type: currentField.type,
                value: String(currentField.value),
              });
              break;
            }
          }
        }

        // push all other fields in
        if (currentField.type !== 'value') {
          nonDefaultFields.push({
            name: fieldName,
            type: currentField.type,
            value: String(currentField.value),
          });
        }
      });

      // for fields that have are now non-default, also save derived values that
      // are not expressed in the UI, for example if link colour has changed
      // then also save data for its programatically generated 'hover' state
      const derivedFields = this.resolveDerivedFields(
        currentValues,
        nonDefaultFields,
        ['hover', 'focus', 'active']
      );

      data.fields = nonDefaultFields.concat(derivedFields);
      return data;
    },

    /**
     * Checks if there are state related CSS variables for all current Form
     * fields exposed in the UI, for example hover states, and resolves a value
     * for each one found.
     *
     * @param {Object} currentValues
     * @param {Array} nonDefaultFields
     * @param {Array} states
     * @return {Array}
     **/
    resolveDerivedFields(currentValues, nonDefaultFields, states) {
      let derivedFields = [];
      nonDefaultFields.map(field => {
        let variableName = field.name;
        states.map(state => {
          let stateObj = this.mergedDefaultCssVariableData[
            variableName + '-' + state
          ];
          if (stateObj) {
            let variableData = Object.assign(stateObj, {});
            variableData.transform.type = 'value';
            variableData.transform.source = currentValues[variableName].value;
            // add a resolved variable object for the given state to our Array
            derivedFields.push({
              name: variableName + '-' + state,
              type: 'value',
              value: String(
                this.theme_settings.resolveCSSVariableValue(
                  variableData,
                  currentValues
                )
              ),
            });
          }
        });
      });
      return derivedFields;
    },
  },
};
</script>

<lang-strings>
{
  "totara_tui": [
    "form_details_default",
    "formcolours_label_primary",
    "formcolours_details_primary",
    "formcolours_label_useoverrides",
    "formcolours_details_useoverrides",
    "formcolours_label_accent",
    "formcolours_details_accent",
    "formcolours_label_primarybuttons",
    "formcolours_details_primarybuttons",
    "formcolours_label_secondarybuttons",
    "formcolours_details_secondarybuttons",
    "formcolours_label_links",
    "formcolours_details_links",
    "formcolours_moresettings",
    "formcolours_label_headerbg",
    "formcolours_details_headerbg",
    "formcolours_label_headertext",
    "formcolours_details_headertext",
    "formcolours_label_pagetext",
    "formcolours_details_pagetext",
    "tabcolours"
  ],
  "totara_core": [
    "save",
    "saveextended",
    "settings"
  ]
}
</lang-strings>
