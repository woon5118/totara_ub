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
  @package performelement_multi_choice_single
-->
<template>
  <FormScope :path="path">
    <FormRadioGroup :validate="answerValidator" name="answer_option">
      <Radio
        v-for="item in element.data.options"
        :key="item.name"
        :value="item.name"
        >{{ item.value }}</Radio
      >
    </FormRadioGroup>
  </FormScope>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import Radio from 'totara_core/components/form/Radio';
import FormRadioGroup from 'totara_core/components/uniform/FormRadioGroup';

export default {
  components: {
    FormScope,
    Radio,
    FormRadioGroup,
  },

  props: {
    path: [String, Array],
    error: String,
    element: Object,
  },
  methods: {
    /**
     * answer validator based on element config
     *
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.element.is_required) {
        const isEmpty =
          !val || (typeof val === 'string' && val.trim().length === 0);
        if (isEmpty)
          return this.$str(
            'error_you_must_answer_this_question',
            'performelement_multi_choice_single'
          );
      }
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_multi_choice_single": [
      "error_you_must_answer_this_question"
    ]
  }
</lang-strings>
