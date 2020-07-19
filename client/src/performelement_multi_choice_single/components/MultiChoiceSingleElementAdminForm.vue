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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module performelement_multi_choice_single
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <div class="tui-elementEditMultiChoiceSingle">
        <Uniform
          v-if="initialValues"
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow
            :label="
              $str('question_title', 'performelement_multi_choice_single')
            "
          >
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow
            :label="
              $str(
                'single_select_options',
                'performelement_multi_choice_single'
              )
            "
          >
            <FieldArray v-slot="{ items, push, remove }" path="answers">
              <Repeater
                :rows="items"
                :min-rows="minRows"
                :delete-icon="true"
                :allow-deleting-first-items="false"
                @add="push()"
                @remove="(item, i) => remove(i)"
              >
                <template v-slot="{ row, index }">
                  <div class="tui-elementEditMultiChoiceSingle__option">
                    <FormText
                      :name="[index]"
                      :validations="v => [v.required()]"
                      :aria-label="
                        $str(
                          'answer_text',
                          'performelement_multi_choice_single',
                          index + 1
                        )
                      "
                    />
                  </div>
                </template>
                <template v-slot:add>
                  <ButtonIcon
                    :aria-label="$str('add', 'moodle')"
                    :styleclass="{ small: true }"
                    class="tui-elementEditMultiChoiceSingle__add-option"
                    @click="push()"
                  >
                    <AddIcon />
                  </ButtonIcon>
                </template>
              </Repeater>
            </FieldArray>
          </FormRow>
          <FormRow>
            <Checkbox v-model="responseRequired" name="responseRequired">
              {{ $str('section_element_response_required', 'mod_perform') }}
            </Checkbox>
          </FormRow>
          <IdentifierInput />
          <FormRow>
            <div class="tui-elementEditMultiChoiceSingle__action-buttons">
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
import AddIcon from 'totara_core/components/icons/common/Add';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Checkbox from 'totara_core/components/form/Checkbox';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import FormText from 'totara_core/components/uniform/FormText';
import IdentifierInput from 'mod_perform/components/element/admin_form/IdentifierInput';
import Repeater from 'totara_core/components/form/Repeater';
import { Uniform, FormRow, FieldArray } from 'totara_core/components/uniform';

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
    FormText,
    IdentifierInput,
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
      initialValues.answers = ['', ''];
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
     * Handle multi choice single element submit data
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
        data: { options: optionList },
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
    "performelement_multi_choice_single": [
        "error_question_required",
        "question_title",
        "answer_text",
        "single_select_options"
    ],
    "mod_perform": [
        "section_element_response_required"
    ],
    "moodle": [
      "add",
      "delete"
     ]
  }
</lang-strings>
