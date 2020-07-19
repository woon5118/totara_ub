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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @module performelement_long_text
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <div class="tui-elementEditLongText">
        <Uniform
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow :label="$str('title', 'performelement_long_text')">
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow
            :label="$str('answer_placeholder', 'performelement_long_text')"
            :hidden="true"
          >
            <Textarea
              :rows="6"
              :disabled="true"
              :placeholder="
                $str('answer_placeholder', 'performelement_long_text')
              "
            />
          </FormRow>
          <FormRow>
            <Checkbox v-model="responseRequired" name="responseRequired">
              {{ $str('section_element_response_required', 'mod_perform') }}
            </Checkbox>
          </FormRow>
          <IdentifierInput />
          <FormRow>
            <div class="tui-elementEditLongText__action-buttons">
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
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import Checkbox from 'tui/components/form/Checkbox';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import IdentifierInput from 'mod_perform/components/element/admin_form/IdentifierInput';
import Textarea from 'tui/components/form/Textarea';
import { Uniform, FormRow, FormText } from 'tui/components/uniform';

export default {
  components: {
    Checkbox,
    ElementAdminForm,
    FormActionButtons,
    FormRow,
    FormText,
    IdentifierInput,
    Textarea,
    Uniform,
  },
  mixins: [AdminFormMixin],
  props: {
    type: Object,
    title: String,
    rawTitle: String,
    identifier: String,
    data: Object,
    isRequired: {
      type: Boolean,
      default: false,
    },
    activityState: {
      type: Object,
      required: true,
    },
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
    handleSubmit(values) {
      this.$emit('update', {
        title: values.rawTitle,
        identifier: values.identifier,
        data: {},
        is_required: this.responseRequired,
      });
    },

    cancel() {
      this.$emit('display');
    },
  },
};
</script>

<lang-strings>
  {
    "performelement_long_text": [
      "answer_placeholder",
      "error_question_required",
      "title"
    ],
    "mod_perform": [
      "section_element_response_required"
    ]
  }
</lang-strings>
