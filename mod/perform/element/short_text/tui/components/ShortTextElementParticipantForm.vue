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
  @package performelement_short_text
-->
<template>
  <FormScope :path="path" :process="process">
    <FormText
      name="answer_text"
      :validations="v => [answerRequired, maxLength]"
    />
  </FormScope>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import { FormText } from 'totara_core/components/uniform';
import { v as validation } from 'totara_core/validation';

export default {
  components: {
    FormScope,
    FormText,
  },

  props: {
    path: [String, Array],
    error: String,
    element: Object,
  },
  methods: {
    process(value) {
      if (!value) {
        return { answer_text: '' };
      }

      value.answer_text = value.answer_text.trim();

      return value;
    },

    /**
     * answer validator based on element config
     *
     * @return {function[]}
     */
    answerRequired(val) {
      if (this.element.is_required) {
        const requiredValidation = validation.required();

        if (requiredValidation.validate(val)) {
          return null;
        }

        return this.$str(
          'error_you_must_answer_this_question',
          'performelement_short_text'
        );
      }
    },

    /**
     * Slightly tweaked maxLength validator to support the fact val may not be set at all.
     *
     * @param val
     * @return {string|null}
     */
    maxLength(val) {
      if (!val) {
        return null;
      }

      const maxLengthValidation = validation.maxLength(1024);

      if (maxLengthValidation.validate(val)) {
        return null;
      }

      return maxLengthValidation.message();
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_short_text": [
    "error_you_must_answer_this_question"
  ]
  }
</lang-strings>
