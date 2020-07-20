<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
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
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import FormRow from 'tui/components/form/FormRow';
import Form from 'tui/components/form/Form';

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
