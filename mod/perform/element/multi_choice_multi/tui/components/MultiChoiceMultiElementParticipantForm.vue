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
  <FormScope :path="path">
    <div>
      <div
        v-if="
          element.data.settings[0].value !== '' &&
            element.data.settings[1].value !== '' &&
            element.data.settings[0].value === element.data.settings[1].value
        "
      >
        {{
          $str(
            'participant_restriction_min_max',
            'performelement_multi_choice_multi',
            element.data.settings[0].value
          )
        }}
      </div>
      <div
        v-else-if="
          element.data.settings[0].value !== '' &&
            element.data.settings[1].value !== ''
        "
      >
        {{
          $str(
            'participant_restriction_min',
            'performelement_multi_choice_multi',
            element.data.settings[0].value
          )
        }}<br />{{
          $str(
            'participant_restriction_max',
            'performelement_multi_choice_multi',
            element.data.settings[1].value
          )
        }}
      </div>
      <div v-else-if="element.data.settings[0].value !== ''">
        {{
          $str(
            'participant_restriction_min',
            'performelement_multi_choice_multi',
            element.data.settings[0].value
          )
        }}
      </div>
      <div v-else-if="element.data.settings[1].value !== ''">
        {{
          $str(
            'participant_restriction_max',
            'performelement_multi_choice_multi',
            element.data.settings[1].value
          )
        }}
      </div>
      <FormCheckboxGroup :validate="answerValidator" name="answer_option">
        <Checkbox
          v-for="item in element.data.options"
          :key="item.name"
          :value="item.name"
          >{{ item.value }}</Checkbox
        >
      </FormCheckboxGroup>
    </div>
  </FormScope>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import FormCheckboxGroup from 'totara_core/components/uniform/FormCheckboxGroup';
import Checkbox from 'totara_core/components/form/Checkbox';
import { v as validation } from 'totara_core/validation';

export default {
  components: {
    Checkbox,
    FormScope,
    FormCheckboxGroup,
  },

  props: {
    path: [String, Array],
    error: String,
    element: Object,
  },
  methods: {
    /**
     * answer validator based on element config
     * @return {function[]}
     */
    answerValidator(val) {
      if (this.element.is_required) {
        const requiredValidation = validation.required();
        if (requiredValidation.validate(val)) {
          if (typeof this.element.data.settings !== 'undefined') {
            return this.valid_for_restriction(val, this.element.data.settings);
          }
          return null;
        }
        return this.$str(
          'error_you_must_answer_this_question',
          'performelement_multi_choice_multi'
        );
      } else {
        // if it isn't required, but participant try submit the data and we have the restrictions.
        if (val.length > 0) {
          if (typeof this.element.data.settings !== 'undefined') {
            return this.valid_for_restriction(val, this.element.data.settings);
          }
        }
        return null;
      }
    },

    valid_for_restriction(val, settings) {
      let min = settings[0].value;
      let max = settings[1].value;
      if (min !== '' && max !== '') {
        if (min === max && (val.length < min || val.length > max)) {
          return this.$str(
            'participant_restriction_min_max',
            'performelement_multi_choice_multi',
            min
          );
        }
      }
      if (min !== '' && val.length < min) {
        return this.$str(
          'participant_restriction_min',
          'performelement_multi_choice_multi',
          min
        );
      }
      if (max !== '' && val.length > max) {
        return this.$str(
          'participant_restriction_max',
          'performelement_multi_choice_multi',
          max
        );
      }
      return null;
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_multi_choice_multi": [
  "error_you_must_answer_this_question",
  "participant_restriction_min_max",
  "participant_restriction_min",
  "participant_restriction_max"
  ]
  }
</lang-strings>
