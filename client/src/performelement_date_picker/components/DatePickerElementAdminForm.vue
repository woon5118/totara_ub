<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @module performelement_date_picker
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <div class="tui-elementEditDatePicker">
        <Uniform
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow
            :label="$str('question_title', 'performelement_date_picker')"
          >
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow :label="$str('date', 'performelement_date_picker')">
            <FormDateSelector
              name="date"
              :initial-current-date="false"
              :disabled="true"
            />
          </FormRow>
          <FormRow>
            <Checkbox v-model="responseRequired" name="responseRequired">
              {{ $str('section_element_response_required', 'mod_perform') }}
            </Checkbox>
          </FormRow>
          <IdentifierInput />
          <FormRow>
            <div class="tui-elementEditDatePicker__action-buttons">
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
  FormDateSelector,
} from 'tui/components/uniform';
import FormText from 'tui/components/uniform/FormText';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import IdentifierInput from 'mod_perform/components/element/admin_form/IdentifierInput';
import Checkbox from 'tui/components/form/Checkbox';

export default {
  components: {
    ElementAdminForm,
    FormActionButtons,
    Uniform,
    FormRow,
    FormText,
    FormDateSelector,
    IdentifierInput,
    Checkbox,
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
    const initialValues = {
      title: this.title,
      rawTitle: this.rawTitle,
      identifier: this.identifier,
      responseRequired: this.isRequired,
    };

    return {
      initialValues: initialValues,
      responseRequired: this.isRequired,
    };
  },
  methods: {
    /**
     * Handle date picker element submit data
     * @param values
     */
    handleSubmit(values) {
      this.$emit('update', {
        title: values.rawTitle,
        identifier: values.identifier,
        data: {},
        is_required: this.responseRequired,
      });
    },

    /**
     * Cancel edit form
     */
    cancel() {
      this.$emit('display');
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_date_picker": [
        "date",
        "question_title"
    ],
    "mod_perform": [
        "section_element_response_required"
    ]
  }
</lang-strings>
