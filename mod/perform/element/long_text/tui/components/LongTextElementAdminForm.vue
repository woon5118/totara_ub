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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @package performelement_long_text
-->
<template>
  <ElementAdminForm :type="type" :error="error" @remove="$emit('remove')">
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
import { Uniform, FormRow, FormText } from 'totara_core/components/uniform';
import Textarea from 'totara_core/components/form/Textarea';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import Checkbox from 'totara_core/components/form/Checkbox';

export default {
  components: {
    ElementAdminForm,
    Uniform,
    FormRow,
    FormText,
    Textarea,
    FormActionButtons,
    Checkbox,
  },
  mixins: [AdminFormMixin],
  props: {
    type: Object,
    title: String,
    rawTitle: String,
    data: Object,
    isRequired: {
      type: Boolean,
      default: false,
    },
    error: String,
  },
  data() {
    const initialValues = {
      title: this.title,
      rawTitle: this.rawTitle,
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
