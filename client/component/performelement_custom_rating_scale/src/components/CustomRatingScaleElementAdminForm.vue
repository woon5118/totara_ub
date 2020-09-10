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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @package performelement_custom_rating_scale
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <Uniform
        v-slot="{ getSubmitting }"
        :initial-values="initialValues"
        :vertical="true"
        class="tui-elementEditCustomRatingScale"
        input-width="full"
        @submit="handleSubmit"
      >
        <FormRow
          :label="$str('question_title', 'performelement_custom_rating_scale')"
          required
        >
          <FormText
            name="rawTitle"
            :validations="v => [v.required(), v.maxLength(1024)]"
            :char-length="30"
          />
        </FormRow>

        <FormRow
          v-slot="{ labelId }"
          :label="
            $str('custom_rating_options', 'performelement_custom_rating_scale')
          "
          :helpmsg="
            $str('custom_values_help', 'performelement_custom_rating_scale')
          "
          required
        >
          <FieldArray v-slot="{ items, push, remove }" path="answers">
            <Repeater
              :rows="items"
              :min-rows="minRows"
              :delete-icon="true"
              :allow-deleting-first-items="false"
              :aria-labelledby="labelId"
              @add="push()"
              @remove="(item, i) => remove(i)"
            >
              <template v-slot:header>
                <InputSet split char-length="30">
                  <InputSetCol units="5">
                    <Label
                      :label="
                        $str('text', 'performelement_custom_rating_scale')
                      "
                      subfield
                    />
                  </InputSetCol>
                  <InputSetCol>
                    <Label
                      :label="
                        $str('score', 'performelement_custom_rating_scale')
                      "
                      subfield
                    />
                  </InputSetCol>
                </InputSet>
              </template>

              <template v-slot="{ index }">
                <InputSet split char-length="30">
                  <InputSetCol units="5">
                    <FormText
                      :name="[index, 'text']"
                      :validations="v => [v.required(), v.maxLength(1024)]"
                      :aria-label="
                        $str(
                          'answer_text',
                          'performelement_custom_rating_scale',
                          {
                            index: index + 1,
                          }
                        )
                      "
                    />
                  </InputSetCol>

                  <InputSetCol>
                    <FormNumber
                      :name="[index, 'score']"
                      :validations="v => [v.required()]"
                      :aria-label="
                        $str(
                          'answer_score',
                          'performelement_custom_rating_scale',
                          {
                            index: index + 1,
                          }
                        )
                      "
                    />
                  </InputSetCol>
                </InputSet>
              </template>
              <template v-slot:add>
                <ButtonIcon
                  :aria-label="$str('add', 'core')"
                  :styleclass="{ small: true }"
                  class="tui-elementEditCustomRatingScale__addOption"
                  @click="push()"
                >
                  <AddIcon />
                </ButtonIcon>
              </template>
            </Repeater>
          </FieldArray>
        </FormRow>

        <IdentifierInput />

        <div class="tui-elementEditCustomRatingScale__required">
          <Checkbox v-model="responseRequired" name="responseRequired">
            {{ $str('section_element_response_required', 'mod_perform') }}
          </Checkbox>
        </div>

        <FormRow class="tui-elementEditCustomRatingScale__action-buttons">
          <FormActionButtons :submitting="getSubmitting()" @cancel="cancel" />
        </FormRow>
      </Uniform>
    </template>
  </ElementAdminForm>
</template>

<script>
import AddIcon from 'tui/components/icons/Add';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Checkbox from 'tui/components/form/Checkbox';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import IdentifierInput from 'mod_perform/components/element/admin_form/IdentifierInput';
import InputSet from 'tui/components/form/InputSet';
import InputSetCol from 'tui/components/form/InputSetCol';
import Label from 'tui/components/form/Label';
import Repeater from 'tui/components/form/Repeater';
import {
  Uniform,
  FormRow,
  FieldArray,
  FormNumber,
  FormText,
} from 'tui/components/uniform';

const MIN_OPTIONS = 2;
const OPTION_PREFIX = 'option_';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    Checkbox,
    ElementAdminForm,
    FieldArray,
    FormActionButtons,
    FormRow,
    FormNumber,
    FormText,
    IdentifierInput,
    InputSet,
    InputSetCol,
    Label,
    Repeater,
    Uniform,
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
    rawData: Object,
    error: String,
  },
  data() {
    const initialValues = {
      title: this.title,
      rawTitle: this.rawTitle,
      identifier: this.identifier,
      responseRequired: this.isRequired,
      answers: [],
    };

    if (Object.keys(this.rawData).length == 0) {
      initialValues.answers = [
        { text: '', score: null },
        { text: '', score: null },
      ];
    } else {
      this.rawData.options.forEach(item => {
        initialValues.answers.push(item.value);
      });
    }

    return {
      initialValues: initialValues,
      minRows: MIN_OPTIONS,
      responseRequired: this.isRequired,
    };
  },
  methods: {
    /**
     * Handle custom rating scale submit data
     * @param values
     */
    handleSubmit(values) {
      const optionList = [];

      values.answers.forEach((item, index) => {
        optionList.push({ name: OPTION_PREFIX + index, value: item });
      });

      this.$emit('update', {
        title: values.rawTitle,
        identifier: values.identifier,
        data: {
          options: optionList,
        },
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
"performelement_custom_rating_scale": [
  "answer_score",
  "answer_text",
  "custom_rating_options",
  "custom_values_help",
  "question_title",
  "score",
  "text"
],
"mod_perform": [
  "section_element_response_required"
]
}
</lang-strings>
