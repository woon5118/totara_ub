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
  @package performelement_multi_choice_multi
-->
<template>
  <ElementAdminForm
    :type="type"
    :error="error"
    :activity-state="activityState"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <div class="tui-elementEditMultiChoiceMulti">
        <Uniform
          v-if="initialValues"
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          :validate="validator"
          @submit="handleSubmit"
        >
          <FormRow
            :label="$str('question_title', 'performelement_multi_choice_multi')"
          >
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow
            :label="
              $str('multi_select_options', 'performelement_multi_choice_multi')
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
                  <div class="tui-elementEditMultiChoiceMulti__option">
                    <FormText
                      :name="[index]"
                      :validations="v => [v.required()]"
                      :aria-label="
                        $str(
                          'answer_text',
                          'performelement_multi_choice_multi',
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
                    class="tui-elementEditMultiChoiceMulti__add-option"
                    @click="push()"
                  >
                    <AddIcon />
                  </ButtonIcon>
                </template>
              </Repeater>
            </FieldArray>
          </FormRow>

          <FormRow
            :label="
              $str('response_restriction', 'performelement_multi_choice_multi')
            "
            class="tui-elementEditMultiChoiceMulti__restriction"
          >
            <div class="tui-elementEditMultiChoiceMulti__respondent">
              <div>
                <FormNumber name="min" />{{
                  $str(
                    'restriction_minimum_label',
                    'performelement_multi_choice_multi'
                  )
                }}
              </div>
              <div>
                <FormNumber name="max" />{{
                  $str(
                    'restriction_maximum_label',
                    'performelement_multi_choice_multi'
                  )
                }}
              </div>
            </div>
          </FormRow>

          <FormRow>
            <Checkbox v-model="responseRequired" name="responseRequired">
              {{ $str('section_element_response_required', 'mod_perform') }}
            </Checkbox>
          </FormRow>
          <IdentifierInput />
          <FormRow>
            <div class="tui-elementEditMultiChoiceMulti__action-buttons">
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
import FormNumber from 'totara_core/components/uniform/FormNumber';
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
    FormNumber,
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
    min: Number,
    max: Number,
  },
  data() {
    const initialValues = {
      title: this.title,
      rawTitle: this.rawTitle,
      identifier: this.identifier,
      responseRequired: this.isRequired,
      answers: [],
      min: '',
      max: '',
    };

    if (Object.keys(this.rawData).length == 0) {
      initialValues.answers = ['', ''];
    } else {
      this.rawData.options.forEach(item => {
        initialValues.answers.push(item.value);
      });
      if (typeof this.rawData.settings !== 'undefined') {
        if (Object.keys(this.rawData.settings).length > 0) {
          this.rawData.settings.forEach(item => {
            if (item.name == 'min') {
              initialValues.min = item.value;
            } else if (item.name == 'max') {
              initialValues.max = item.value;
            }
          });
        }
      }
    }

    return {
      initialValues: initialValues,
      minRows: MIN_OPTIONS,
      responseRequired: this.isRequired,
    };
  },
  methods: {
    /**
     * Handle multi choice element submit data
     * @param values
     */
    handleSubmit(values) {
      const optionList = [];
      const restrictionVal = [];

      values.answers.forEach((item, index) => {
        optionList.push({ name: OPTION_PREFIX + index, value: item });
      });

      restrictionVal.push({ name: 'min', value: values.min });
      restrictionVal.push({ name: 'max', value: values.max });

      this.$emit('update', {
        title: values.rawTitle,
        identifier: values.identifier,
        data: {
          options: optionList,
          settings: restrictionVal,
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

    validator(values) {
      const errors = {};
      if (values.min !== '' && Number(values.min) <= 0) {
        errors.min = this.$str(
          'minmaximum_zero_error',
          'performelement_multi_choice_multi'
        );
      }
      if (values.max !== '' && Number(values.max) <= 0) {
        errors.max = this.$str(
          'minmaximum_zero_error',
          'performelement_multi_choice_multi'
        );
      }
      if (values.min !== '' && values.answers.length < Number(values.min)) {
        errors.min = this.$str(
          'minmaximum_value_error',
          'performelement_multi_choice_multi'
        );
      }
      if (values.max !== '' && values.answers.length < Number(values.max)) {
        errors.max = this.$str(
          'minmaximum_value_error',
          'performelement_multi_choice_multi'
        );
      }
      if (
        values.min !== '' &&
        values.max !== '' &&
        Number(values.min) > Number(values.max)
      ) {
        errors.max = this.$str(
          'minmaximum_less_error',
          'performelement_multi_choice_multi'
        );
      }
      return errors;
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_multi_choice_multi": [
  "answer_text",
  "error_question_required",
  "question_title",
  "minmaximum_less_error",
  "minmaximum_value_error",
  "minmaximum_zero_error",
  "multi_select_options",
  "response_restriction",
  "restriction_minimum_label",
  "restriction_maximum_label"
  ],
  "mod_perform": [
    "section_element_response_required"
  ]
  }
</lang-strings>
