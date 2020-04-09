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
  @package totara_core
-->

<template>
  <Uniform
    v-slot="{ getSubmitting }"
    :initial-values="initialValues"
    :errors="errors"
    :validate="validate"
    @change="handleChange"
    @submit="submit"
  >
    <FormRow label="Title" required>
      <FormText name="title" :validations="v => [v.required()]" />
    </FormRow>

    <!-- or specify the input by hand -->
    <FormRow label="Length" required>
      <FormField
        v-slot="{ id, value, update, blur }"
        name="length"
        :validations="v => [v.required(), v.number()]"
      >
        <InputText
          :id="id"
          :value="value"
          @input="value => update(value)"
          @blur="blur"
        />
      </FormField>
    </FormRow>

    <FormRow label="Age">
      <FormNumber name="age" />
    </FormRow>

    <FormRow label="Bread" required>
      <FormRadioGroup name="bread" :validations="v => [v.required()]">
        <Radio value="chorleywood">Chorleywood</Radio>
        <Radio value="ciabatta">Ciabatta</Radio>
        <Radio value="rye">Rye</Radio>
        <Radio value="sourdough">Sourdough</Radio>
      </FormRadioGroup>
    </FormRow>

    <FormRowFieldset label="Answers">
      <FieldArray v-slot="{ items, push, remove }" path="answers">
        <Repeater
          :rows="items"
          :min-rows="1"
          :delete-icon="true"
          :allow-deleting-first-items="true"
          @add="push('')"
          @remove="(item, i) => remove(i)"
        >
          <template v-slot="{ row, index }">
            <FormText
              :name="index"
              :validations="v => [v.required()]"
              aria-label="Answer text"
            />
          </template>
        </Repeater>
      </FieldArray>
    </FormRowFieldset>

    <SampleFormPart path="fullName" />

    <FormRowActionButtons :submitting="getSubmitting()" @cancel="cancel" />

    <h3 v-if="result">Result</h3>
    <pre v-if="result">{{ result }}</pre>
  </Uniform>
</template>

<script>
import {
  Uniform,
  FormField,
  FieldArray,
  FormRow,
  FormRowFieldset,
  FormText,
  FormNumber,
  FormRadioGroup,
} from 'totara_core/components/uniform';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';
import SampleFormPart from 'totara_samples/components/sample_parts/totara_core/form/FormPart';
import Repeater from 'totara_core/components/form/Repeater';

export default {
  components: {
    Uniform,
    FormField,
    FieldArray,
    FormRow,
    FormRowFieldset,
    FormText,
    FormNumber,
    FormRadioGroup,
    InputText,
    Radio,
    FormRowActionButtons,
    SampleFormPart,
    Repeater,
  },

  data() {
    return {
      initialValues: {
        answers: ['first value', '', 'third value'],
      },
      errors: null,
      result: null,
    };
  },

  methods: {
    validate(values) {
      const errors = {};

      if (values.title && values.title.toLowerCase().includes('a')) {
        errors.title = 'Please do not use the letter "a"';
      }

      return errors;
    },

    handleChange() {
      if (this.errors) {
        this.errors = null;
      }
    },

    submit(values) {
      if (values.title && values.title.includes('server')) {
        this.errors = { title: 'Title must not include "server"' };
        return;
      }
      if (this.errors) {
        this.errors = null;
      }
      this.result = values;
    },

    cancel() {
      //
    },
  },
};
</script>
