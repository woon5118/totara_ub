<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_samples
-->

<template>
  <div>
    An example form made up of several components

    <SamplesExample>
      <Form>
        <FormRow v-slot="{ id, label }" label="Name">
          <InputText
            :id="id"
            v-model="name"
            :disabled="disabled"
            :placeholder="label"
          />
        </FormRow>

        <FormRow label="Colour">
          <InputColor v-model="hexColour" />
        </FormRow>

        <FormRow label="Favourite group">
          <RadioGroup v-model="colour" :horizontal="true" :disabled="disabled">
            <Radio value="red">
              Red
            </Radio>
            <Radio value="green">
              Green
            </Radio>
            <Radio value="blue">
              Blue
            </Radio>
            <Radio value="yellow">
              Yellow
            </Radio>
          </RadioGroup>
        </FormRow>

        <FormRow v-slot="{ id }" label="Select year">
          <Select
            :id="id"
            v-model="select"
            :options="[
              { label: '1950', id: 1 },
              { label: '1960', id: 2 },
            ]"
            :disabled="disabled"
          />
        </FormRow>

        <FormRow v-slot="{ id }" label="Comment">
          <Textarea :id="id" v-model="comment" :disabled="disabled" :rows="4" />
        </FormRow>

        <FormRow>
          <Checkbox v-model="terms" :disabled="disabled">
            I agree to the
            <a :href="$url('/terms.php')">Terms and Conditions</a>
          </Checkbox>
        </FormRow>

        <FormRowActionButtons @cancel="formCancel" @submit="formSubmit" />
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
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

import Checkbox from 'totara_core/components/form/Checkbox';
import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';
import InputText from 'totara_core/components/form/InputText';
import InputColor from 'totara_core/components/form/InputColor';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Select from 'totara_core/components/form/Select';
import Textarea from 'totara_core/components/form/Textarea';

export default {
  components: {
    Form,
    FormRow,
    FormRowActionButtons,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
    Checkbox,
    InputText,
    Radio,
    RadioGroup,
    Select,
    Textarea,
    InputColor,
  },

  data() {
    return {
      name: '',
      colour: '',
      hexColour: '#000000',
      comment: '',
      select: 1,
      terms: '',
      disabled: false,

      codeTemplate: `<Form>
  <FormRow v-slot="{ id, label }" label="Name">
    <InputText
      :id="id"
      v-model="name"
      :disabled="disabled"
      :placeholder="label"
    />
  </FormRow>

  <FormRow label="Favourite group">
    <RadioGroup v-model="colour" :horizontal="true" :disabled="disabled">
      <Radio value="red">
        Red
      </Radio>
      <Radio value="green">
        Green
      </Radio>
      <Radio value="blue">
        Blue
      </Radio>
      <Radio value="yellow">
        Yellow
      </Radio>
    </RadioGroup>
  </FormRow>

  <FormRow v-slot="{ id }" label="Select year">
    <Select
      :id="id"
      v-model="select"
      :options="[
        { label: '1950', id: 1 },
        { label: '1960', id: 2 },
      ]"
      :disabled="disabled"
    />
  </FormRow>

  <FormRow v-slot="{ id }" label="Comment">
    <Textarea :id="id" v-model="comment" :disabled="disabled" :rows="4" />
  </FormRow>

  <FormRow>
    <Checkbox v-model="terms" :disabled="disabled">
      I agree to the
      <a :href="$url('/terms.php')">Terms and Conditions</a>
    </Checkbox>
  </FormRow>

  <FormRowActionButtons @cancel="formCancel" @submit="formSubmit" />

</Form>`,
      codeScript: `import Checkbox from 'totara_core/components/form/Checkbox';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Select from 'totara_core/components/form/Select';
import Textarea from 'totara_core/components/form/Textarea';

export default {
  components: {
    Checkbox,
    Form,
    FormRow,
    FormRowActionButtons,
    InputText,
    Radio,
    RadioGroup,
    Select,
    Textarea,
  }

  data() {
    return {
      colour: '',
      comment: '',
      disabled: false,
      name: '',
      select: '',
      terms: '',
    }
  },

  methods: {
    formCancel() {
      this.disabled = false;
    },

    formSubmit() {
      this.disabled = true;
    },
  },
}`,
    };
  },

  methods: {
    formCancel() {
      this.disabled = false;
    },

    formSubmit() {
      this.disabled = true;
    },
  },
};
</script>
