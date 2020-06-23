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
  along with this program. If not, see <http://www.gnu.org/licenses/>.

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @package performelement_static_content
-->
<template>
  <ElementAdminForm :type="type" :error="error" @remove="$emit('remove')">
    <template v-slot:content>
      <div class="tui-elementEditStaticContent">
        <Uniform
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow :label="$str('title', 'performelement_static_content')">
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow
            :label="
              $str(
                'static_content_placeholder',
                'performelement_static_content'
              )
            "
          >
            <FormTextarea
              name="rawText"
              :rows="6"
              :validations="v => [v.required()]"
            />
          </FormRow>
          <FormRow>
            <div class="tui-elementEditStaticContent__action-buttons">
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
import FormTextarea from 'totara_core/components/uniform/FormTextarea';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';

export default {
  components: {
    ElementAdminForm,
    Uniform,
    FormTextarea,
    FormRow,
    FormText,
    FormActionButtons,
  },
  mixins: [AdminFormMixin],
  props: {
    type: Object,
    title: String,
    rawTitle: String,
    data: Object,
    rawData: Object,
    error: String,
  },
  data() {
    const initialValues = {
      title: this.title,
      rawTitle: this.rawTitle,
      data: this.data,
    };
    if (Object.keys(this.rawData).length == 0) {
      initialValues.rawText = '';
    } else {
      initialValues.rawText = this.rawData.textValue;
    }
    return {
      initialValues: initialValues,
    };
  },
  methods: {
    handleSubmit(values) {
      this.$emit('update', {
        title: values.rawTitle,
        data: { textValue: values.rawText.trim() },
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
  "performelement_static_content": [
  "title",
  "error_you_must_fill_the_area",
  "static_content_placeholder"
  ]
  }
</lang-strings>
