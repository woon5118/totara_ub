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

  @author Simon Chester <simon.chester@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <ElementEdit :type="type" :error="error">
    <template v-slot:content>
      <div>
        <FormDynamic
          v-slot="{ values, nativeinput, blur, handleSubmit, formy }"
          :value="initialValue"
          :validate="validate"
          @submit="handleSubmit"
        >
          <Form @submit="handleSubmit">
            <FormRow
              v-slot="{ id, inputName }"
              name="name"
              :label="$str('short_text:title', 'performelement_short_text')"
              :formy="formy"
            >
              <InputText
                :id="id"
                :name="inputName"
                :value="values[inputName]"
                v-on="{ nativeinput, blur }"
              />
            </FormRow>
            <FormRow
              name="answer"
              :label="
                $str(
                  'short_text:answer:placeholder',
                  'performelement_short_text'
                )
              "
              :hidden="true"
            >
              <Textarea
                :disabled="true"
                :placeholder="
                  $str(
                    'short_text:answer:placeholder',
                    'performelement_short_text'
                  )
                "
              />
            </FormRow>
            <FormRow>
              <ButtonGroup>
                <Button
                  :styleclass="{ primary: 'true' }"
                  :text="
                    $str('short_text:button:done', 'performelement_short_text')
                  "
                  type="submit"
                  @click="$emit('click', $event)"
                />
                <Button
                  :text="
                    $str(
                      'short_text:button:cancel',
                      'performelement_short_text'
                    )
                  "
                  @click="cancel"
                />
              </ButtonGroup>
            </FormRow>
          </Form>
        </FormDynamic>
      </div>
    </template>
  </ElementEdit>
</template>

<script>
import ElementEdit from 'mod_perform/components/element/ElementEdit';
import FormDynamic from 'totara_core/components/form/Formy';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import Textarea from 'totara_core/components/form/Textarea';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Button from 'totara_core/components/buttons/Button';

export default {
  components: {
    ElementEdit,
    FormDynamic,
    Form,
    FormRow,
    InputText,
    Textarea,
    ButtonGroup,
    Button,
  },

  props: {
    type: Object,
    name: String,
    data: Object,
    error: String,
  },

  computed: {
    initialValue() {
      return {
        name: this.name,
      };
    },
  },

  methods: {
    validate({ values, errors }) {
      if (!values.name || values.name.trim() == '') {
        errors.name = this.$str(
          'error:question_required',
          'performelement_short_text'
        );
      }
      if (values.name.trim().length > 1024) {
        errors.name = this.$str(
          'error:question_length_exceed',
          'performelement_short_text'
        );
      }
    },

    handleSubmit(values) {
      this.$emit('update', {
        name: values.name,
        data: {},
      });
    },

    cancel() {
      this.$emit('display');
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_short_text": [
        "error:question_required",
        "error:question_length_exceed",
        "short_text:title",
        "short_text:answer:placeholder",
        "short_text:button:done",
        "short_text:button:cancel"
    ]
  }
</lang-strings>
