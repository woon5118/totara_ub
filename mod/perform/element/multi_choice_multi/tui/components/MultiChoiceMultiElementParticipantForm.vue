<!--
  This file is part of Totara Perform

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
  <ElementParticipantForm :name="name">
    <template v-slot:content>
      <FormScope :path="path">
        <FormRow
          :label="$str('your_response', 'performelement_multi_choice_multi')"
        >
          <FormCheckboxGroup :validate="answerValidator" name="answer_option">
            <Checkbox
              v-for="item in data.options"
              :key="item.name"
              :value="item.name"
              >{{ item.value }}</Checkbox
            >
          </FormCheckboxGroup>
        </FormRow>
      </FormScope>
    </template>
  </ElementParticipantForm>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';
import { FormRow } from 'totara_core/components/uniform';
import FormCheckboxGroup from 'totara_core/components/uniform/FormCheckboxGroup';
import Checkbox from 'totara_core/components/form/Checkbox';
import { v as validation } from 'totara_core/validation';

export default {
  components: {
    Checkbox,
    FormScope,
    FormRow,
    ElementParticipantForm,
    FormCheckboxGroup,
  },

  props: {
    path: [String, Array],
    type: Object,
    name: String,
    data: Object,
    isRequired: {
      type: Boolean,
      default: false,
    },
    error: String,
  },
  methods: {
    /**
     * answer validator based on element config
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.isRequired) {
        const requiredValidation = validation.required();
        if (requiredValidation.validate(val)) {
          return null;
        }
        return this.$str(
          'error_you_must_answer_this_question',
          'performelement_multi_choice_multi'
        );
      }
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_multi_choice_multi": [
  "your_response",
  "error_you_must_answer_this_question"
  ]
  }
</lang-strings>
