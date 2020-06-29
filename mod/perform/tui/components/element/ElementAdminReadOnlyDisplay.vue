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
  @package mod_perform
-->
<template>
  <div class="tui-performElementReadOnlyDisplay" tabindex="0">
    <Form :vertical="true" input-width="full">
      <FormRow :label="$str('question_title', 'mod_perform')">
        {{ title }}
      </FormRow>
      <slot name="content" />
      <FormRow
        :label="$str('section_element_response_required', 'mod_perform')"
      >
        <span>
          {{
            $str(
              isRequired
                ? 'question_response_required_yes'
                : 'question_response_required_no',
              'mod_perform'
            )
          }}
        </span>
      </FormRow>
      <FormRow :label="$str('reporting_identifier', 'mod_perform')">
        {{ identifier }}
      </FormRow>
      <FormRow>
        <div class="tui-performElementReadOnlyDisplay__action">
          <ButtonGroup>
            <Button
              :styleclass="{ small: true }"
              text="Close"
              @click="done()"
            />
          </ButtonGroup>
        </div>
      </FormRow>
    </Form>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import FormRow from 'totara_core/components/form/FormRow';
import Form from 'totara_core/components/form/Form';

export default {
  components: {
    Button,
    ButtonGroup,
    FormRow,
    Form,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    type: {
      type: Object,
      required: true,
    },
    isRequired: {
      type: Boolean,
    },
    identifier: String,
  },
  methods: {
    done() {
      this.$emit('display');
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "question_title",
      "question_response_required_yes",
      "question_response_required_no",
      "section_element_response_required",
      "reporting_identifier"
    ]
  }
</lang-strings>
