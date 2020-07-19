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
  @module performelement_static_content
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
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
    activityState: {
      type: Object,
      required: true,
    },
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
  "static_content_placeholder"
  ]
  }
</lang-strings>
