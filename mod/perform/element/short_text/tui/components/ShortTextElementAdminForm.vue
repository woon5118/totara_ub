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

  @author Simon Chester <simon.chester@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package performelement_short_text
-->
<template>
  <ElementAdminForm :type="type" :error="error">
    <template v-slot:content>
      <div class="tui-elementEditShortText">
        <Uniform
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow
            :label="$str('short_text_title', 'performelement_short_text')"
          >
            <FormText
              name="name"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow
            :label="
              $str('short_text_answer_placeholder', 'performelement_short_text')
            "
            :hidden="true"
          >
            <Textarea
              :disabled="true"
              :placeholder="
                $str(
                  'short_text_answer_placeholder',
                  'performelement_short_text'
                )
              "
            />
          </FormRow>
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
import { Uniform, FormRow, FormText } from 'totara_core/components/uniform';
import Textarea from 'totara_core/components/form/Textarea';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';

export default {
  components: {
    ElementAdminForm,
    Uniform,
    FormRow,
    FormText,
    Textarea,
    FormActionButtons,
  },

  props: {
    type: Object,
    name: String,
    data: Object,
    error: String,
  },
  computed: {
    initialValues() {
      return {
        name: this.name,
      };
    },
  },

  methods: {
    handleSubmit(values) {
      this.$emit('update', {
        name: values.name,
        data: {},
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
    "performelement_short_text": [
        "error_question_required",
        "error_question_length_exceed",
        "short_text_title",
        "short_text_answer_placeholder"
    ]
  }
</lang-strings>
