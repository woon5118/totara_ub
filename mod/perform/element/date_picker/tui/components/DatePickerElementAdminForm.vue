<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @package performelement_date_picker
-->
<template>
  <ElementAdminForm :type="type" :error="error" @remove="$emit('remove')">
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
} from 'totara_core/components/uniform';
import FormText from 'totara_core/components/uniform/FormText';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import IdentifierInput from 'mod_perform/components/element/admin_form/IdentifierInput';
import Checkbox from 'totara_core/components/form/Checkbox';

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
