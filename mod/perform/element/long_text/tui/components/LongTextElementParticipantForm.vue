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
  <ElementParticipantForm :name="name">
    <template v-slot:content>
      <div>
        <FormScope :path="path" :process="process">
          <FormRow :label="$str('your_response', 'performelement_long_text')">
            <FormTextarea
              rows="6"
              name="answer_text"
              :validations="v => [answerRequired]"
            />
          </FormRow>
        </FormScope>
      </div>
    </template>
  </ElementParticipantForm>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import { FormRow } from 'totara_core/components/uniform';
import FormTextarea from 'totara_core/components/uniform/FormTextarea';
import ElementParticipantForm from 'mod_perform/components/element/ElementParticipantForm';

export default {
  components: {
    FormScope,
    FormRow,
    FormTextarea,
    ElementParticipantForm,
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
    process(values) {
      values.answer_text = values.answer_text.trim();
      return values;
    },

    /**
     * answer validator based on element config
     *
     * @return {function[]}
     */
    answerRequired(val) {
      if (this.isRequired) {
        const isEmpty =
          !val || (typeof val === 'string' && val.trim().length === 0);
        if (isEmpty) {
          return this.$str(
            'error_you_must_answer_this_question',
            'performelement_long_text'
          );
        }
      }
    },
  },
};
</script>

<lang-strings>
  {
    "performelement_long_text": [
      "your_response",
      "error_you_must_answer_this_question"
    ]
  }
</lang-strings>
