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
  <ElementUserForm>
    <template v-slot:content>
      <div>
        <FormScope :path="path" :process="process">
          <FormRow
            :label="
              $str('short_text_your_response', 'performelement_short_text')
            "
          >
            <FormTextarea
              name="answer_text"
              :validations="v => [v.required(), v.maxLength(1024)]"
            />
          </FormRow>
        </FormScope>
      </div>
    </template>
  </ElementUserForm>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import { FormRow } from 'totara_core/components/uniform';
import FormTextarea from 'totara_core/components/uniform/FormTextarea';
import ElementUserForm from 'mod_perform/components/element/ElementUserForm';

export default {
  components: {
    FormScope,
    FormRow,
    FormTextarea,
    ElementUserForm,
  },

  props: {
    path: [String, Array],
    type: Object,
    name: String,
    data: Object,
    error: String,
  },
  methods: {
    process(values) {
      values.answer_text = values.answer_text.trim();
      return values;
    },
  },
};
</script>
<lang-strings>
  {
  "performelement_short_text": [
    "short_text_your_response"
  ]
  }
</lang-strings>
