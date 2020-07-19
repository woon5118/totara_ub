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
  <div class="tui-testActionLink">
    The action link component is used when styling a link to suggest additional
    actions other than just navigating will be triggered by clicking this.

    <SamplesExample>
      <ActionLink
        href="/"
        :text="text"
        :disabled="disabled"
        :styleclass="{
          primary: primaryStyle,
          small: smallStyle,
          srOnly: screenReaderStyle,
        }"
      />

      <h4>Form</h4>
      <Form :class="'tui-testForm'">
        <FormRow label="Link">
          <ActionLink
            href="/"
            :text="text"
            :disabled="disabled"
            :styleclass="{
              primary: primaryStyle,
              small: smallStyle,
              srOnly: screenReaderStyle,
            }"
          />
        </FormRow>
      </Form>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ id, label }" label="Text">
        <InputText
          :id="id"
          v-model="text"
          :disabled="disabled"
          :placeholder="label"
        />
      </FormRow>

      <FormRow v-slot="{ labelId }" label="Primary">
        <RadioGroup v-model="primaryStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ labelId }" label="Small">
        <RadioGroup v-model="smallStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ labelId }" label="Disabled">
        <RadioGroup v-model="disabled" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ labelId }" label="Screen readers only">
        <RadioGroup v-model="screenReaderStyle" :horizontal="true">
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
// Components
import ActionLink from 'totara_core/components/links/ActionLink';
import Form from 'totara_core/components/form/Form';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    ActionLink,
    Form,
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
      disabled: false,
      primaryStyle: false,
      smallStyle: false,
      screenReaderStyle: false,
      text: 'Action Link',
      codeTemplate: `<ActionLink
  href="/"
  text="Action Link"
  :disabled="disabled"
  :styleclass="{
    primary: primaryStyle,
    small: smallStyle,
    srOnly: screenReaderStyle,
  }"
/>`,
      codeScript: `import ActionLink from 'totara_core/components/links/ActionLink;

export default {
  components: {
    ActionLink,
  }
}`,
    };
  },
};
</script>

<style lang="scss">
.tui-testActionLink {
  h4 {
    margin-top: var(--tui-gap-6);
  }
}
</style>
