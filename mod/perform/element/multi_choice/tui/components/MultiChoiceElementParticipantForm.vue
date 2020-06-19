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
  <ElementParticipantForm :name="name">
    <template v-slot:content>
      <FormScope :path="path">
        <FormRow :label="$str('your_response', 'performelement_multi_choice')">
          <FormRadioGroup :validate="answerValidator" name="answer_option">
            <Radio
              v-for="item in data.options"
              :key="item.name"
              :value="item.name"
              >{{ item.value }}</Radio
            >
          </FormRadioGroup>
        </FormRow>
      </FormScope>
    </template>
  </ElementParticipantForm>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';
import { FormRow } from 'totara_core/components/uniform';
import Radio from 'totara_core/components/form/Radio';
import FormRadioGroup from 'totara_core/components/uniform/FormRadioGroup';

export default {
  components: {
    FormScope,
    FormRow,
    ElementParticipantForm,
    Radio,
    FormRadioGroup,
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
     *
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.isRequired) {
        const isEmpty =
          !val || (typeof val === 'string' && val.trim().length === 0);
        if (isEmpty)
          return this.$str(
            'error_you_must_answer_this_question',
            'performelement_multi_choice'
          );
      }
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_multi_choice": [
      "your_response",
      "error_you_must_answer_this_question"
    ]
  }
</lang-strings>
