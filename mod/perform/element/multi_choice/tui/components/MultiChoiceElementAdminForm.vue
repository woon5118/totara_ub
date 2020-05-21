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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package performelement_multi_choice
-->
<template>
  <ElementAdminForm :type="type" :error="error">
    <template v-slot:content>
      <div class="tui-elementEditMultiChoice">
        <Uniform
          v-if="initialValues"
          v-slot="{ getSubmitting }"
          :initial-values="initialValues"
          :vertical="true"
          input-width="full"
          @submit="handleSubmit"
        >
          <FormRow
            :label="$str('question_title', 'performelement_multi_choice')"
          >
            <FormText
              name="rawTitle"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
          <FormRow
            :label="
              $str('single_select_options', 'performelement_multi_choice')
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
                  <div class="tui-elementEditMultiChoice__option">
                    <FormText
                      :name="[index]"
                      :validations="v => [v.required()]"
                      :aria-label="
                        $str('answer_text', 'performelement_multi_choice')
                      "
                    />
                  </div>
                </template>
                <template v-slot:add>
                  <ButtonIcon
                    :aria-label="$str('add', 'moodle')"
                    :styleclass="{ small: true }"
                    class="tui-elementEditMultiChoice__add-option"
                    @click="push()"
                  >
                    <AddIcon />
                  </ButtonIcon>
                </template>
              </Repeater>
            </FieldArray>
          </FormRow>
          <FormRow>
            <div class="tui-elementEditMultiChoice__action-buttons">
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
import { Uniform, FormRow, FieldArray } from 'totara_core/components/uniform';
import FormText from 'totara_core/components/uniform/FormText';
import ElementAdminForm from 'mod_perform/components/element/ElementAdminForm';
import FormActionButtons from 'mod_perform/components/element/admin_form/ActionButtons';
import AdminFormMixin from 'mod_perform/components/element/admin_form/AdminFormMixin';
import Repeater from 'totara_core/components/form/Repeater';
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';

const MIN_OPTIONS = 2;
const OPTION_PREFIX = 'option_';

export default {
  components: {
    ElementAdminForm,
    FormActionButtons,
    Uniform,
    FormRow,
    FormText,
    Repeater,
    FieldArray,
    AddIcon,
    ButtonIcon,
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
    };
  },
  methods: {
    /**
     * Handle multi choice element submit data
     * @param values
     */
    handleSubmit(values) {
      const optionList = [];

      values.answers.forEach((item, index) => {
        optionList.push({ name: OPTION_PREFIX + index, value: item });
      });
      this.$emit('update', {
        title: values.rawTitle,
        data: { options: optionList },
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
    "performelement_multi_choice": [
        "error_question_required",
        "question_title",
        "answer_text",
        "single_select_options"
    ],
    "moodle": [
      "add",
      "delete"
     ]
  }
</lang-strings>
