<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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
  <div class="tui-testArrow">
    A group of radio buttons

    <SamplesExample>
      <RadioGroup
        v-model="colour"
        :horizontal="horizontal"
        :disabled="disabled"
        :required="required"
        :aria-labelledby="ariaLabelledby"
      >
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

      <h4 class="formHeader">Form</h4>
      <Form>
        <FormRow label="Radio group">
          <RadioGroup
            v-model="food"
            :horizontal="horizontal"
            :disabled="disabled"
            :aria-labelledby="ariaLabelledby"
          >
            <Radio value="eggs">
              Eggs
            </Radio>
            <Radio value="ham">
              Ham
            </Radio>
            <Radio value="cheese">
              Cheese
            </Radio>
            <Radio value="onion">
              Onion
            </Radio>
          </RadioGroup>
        </FormRow>
      </Form>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Horizontal">
        <RadioGroup v-model="horizontal" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>
      <FormRow label="Disabled">
        <RadioGroup v-model="disabled" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>
      <FormRow label="Required">
        <RadioGroup v-model="required" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>
      <FormRow v-slot="{ id }" label="ariaLabelledby">
        <InputText :id="id" v-model="ariaLabelledby" />
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    FormRow,
    InputText,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      colour: 'green',
      food: 'eggs',
      disabled: false,
      horizontal: false,
      required: false,
      ariaLabelledby: '',

      codeTemplate: `<RadioGroup
v-model="colour"
:horizontal="horizontal"
:disabled="disabled"
:required="required"
:aria-labelledby="ariaLabelledby"
>
  <Radio value="red">Red</Radio>
  <Radio value="green">Green</Radio>
  <Radio value="blue">Blue</Radio>
  <Radio value="yellow">Yellow</Radio>
</RadioGroup>`,
      codeScript: `import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';

export default {
  components: {
    Radio,
    RadioGroup,
  }
}`,
    };
  },
};
</script>
