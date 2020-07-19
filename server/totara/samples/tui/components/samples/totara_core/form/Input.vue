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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    A set of standard input field components. Each type (email, number, password
    ...) has it's own stand-alone component

    <SamplesExample>
      <Form>
        <FormRow v-slot="{ id, label }" :label="'InputEmail'">
          <InputEmail
            :id="id"
            v-model="inputEmail"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputHidden'">
          <InputHidden
            :id="id"
            v-model="inputHidden"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputNumber'">
          <InputNumber
            :id="id"
            v-model="inputNumber"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputPassword'">
          <InputPassword
            :id="id"
            v-model="inputPassword"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputSearch'">
          <InputSearch
            :id="id"
            v-model="inputSearch"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputTel'">
          <InputTel
            :id="id"
            v-model="inputTel"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputText'">
          <InputText
            :id="id"
            v-model="inputText"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow v-slot="{ id, label }" :label="'InputURL'">
          <InputUrl
            :id="id"
            v-model="inputUrl"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>
      </Form>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Disabled">
        <RadioGroup v-model="disabled" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

import InputEmail from 'totara_core/components/form/InputEmail';
import InputHidden from 'totara_core/components/form/InputHidden';
import InputNumber from 'totara_core/components/form/InputNumber';
import InputPassword from 'totara_core/components/form/InputPassword';
import InputSearch from 'totara_core/components/form/InputSearch';
import InputTel from 'totara_core/components/form/InputTel';
import InputText from 'totara_core/components/form/InputText';
import InputUrl from 'totara_core/components/form/InputUrl';

export default {
  components: {
    Form,
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,

    InputEmail,
    InputHidden,
    InputNumber,
    InputPassword,
    InputSearch,
    InputTel,
    InputText,
    InputUrl,
  },

  data() {
    return {
      inputEmail: '',
      inputHidden: '',
      inputNumber: '',
      inputPassword: '',
      inputSearch: '',
      inputTel: '',
      inputText: '',
      inputUrl: '',
      disabled: false,

      codeTemplate: `<Form>
  <FormRow v-slot="{ id, label }" label="First name">
    <InputText
      :id="id"
      v-model="enteredText"
      disabled="false"
      :placeholder="label"
    />
  </FormRow>
</Form>`,
      codeScript: `import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';

export default {
  components: {
    Form,
    FormRow,
    InputText
  }

  data() {
    return {
      enteredText: '',
    }
  },
}`,
    };
  },
};
</script>
