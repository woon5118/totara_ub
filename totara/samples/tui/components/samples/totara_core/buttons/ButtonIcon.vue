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
  <div class="tui-testButton">
    The button icon component is used when triggering an on page action.

    <SamplesExample>
      <ButtonIcon
        :aria-label="'Add'"
        :disabled="disabled"
        :caret="caret"
        :styleclass="{
          alert: alertStyle,
          circle: circleStyle,
          primary: primaryStyle,
          small: smallStyle,
          square: squareStyle,
          textFirst: textFirstStyle,
          transparent: transparentStyle,
          stealth: stealthStyle,
          toolbar: toolbarStyle,
          selected: selectedStyle,
          xsmall: xsmallStyle,
          transparentNoPadding: paddingStyle,
        }"
        :text="text"
      >
        <AddIcon />
      </ButtonIcon>

      <h4>Form</h4>
      <Form>
        <FormRow label="Button">
          <ButtonIcon
            :aria-label="'Add'"
            :disabled="disabled"
            :caret="caret"
            :styleclass="{
              alert: alertStyle,
              circle: circleStyle,
              primary: primaryStyle,
              small: smallStyle,
              square: squareStyle,
              textFirst: textFirstStyle,
              transparent: transparentStyle,
              stealth: stealthStyle,
              toolbar: toolbarStyle,
              selected: selectedStyle,
              xsmall: xsmallStyle,
              transparentNoPadding: paddingStyle,
            }"
            :text="text"
          >
            <AddIcon />
          </ButtonIcon>
        </FormRow>
      </Form>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ id, label }" label="Text">
        <InputText :id="id" v-model="text" :placeholder="label" />
      </FormRow>

      <FormRow label="Primary">
        <RadioGroup v-model="primaryStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Alert">
        <RadioGroup v-model="alertStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Small">
        <RadioGroup v-model="smallStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Extra small">
        <RadioGroup v-model="xsmallStyle" :horizontal="true">
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

      <FormRow label="Circle">
        <RadioGroup v-model="circleStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Square">
        <RadioGroup v-model="squareStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Transparent">
        <RadioGroup v-model="transparentStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Stealth">
        <RadioGroup v-model="stealthStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Caret (for dropdown)">
        <RadioGroup v-model="caret" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Text first">
        <RadioGroup v-model="textFirstStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Toolbar">
        <RadioGroup v-model="toolbarStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Selected (for toolbar)">
        <RadioGroup v-model="selectedStyle" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Transparent No padding">
        <RadioGroup v-model="paddingStyle" :horizontal="true">
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
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
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
    AddIcon,
    ButtonIcon,
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
      circleStyle: false,
      disabled: false,
      inputButtonStyle: false,
      alertStyle: false,
      primaryStyle: false,
      textFirstStyle: false,
      smallStyle: false,
      xsmallStyle: false,
      squareStyle: false,
      text: '',
      transparentStyle: false,
      paddingStyle: false,
      stealthStyle: false,
      toolbarStyle: false,
      selectedStyle: false,
      caret: false,
      codeTemplate: `<ButtonIcon
  :aria-label="Add"
  :disabled="disabled"
  :styleclass="{
    alert: alertStyle,
    circle: circleStyle,
    primary: primaryStyle,
    small: smallStyle,
    square: squareStyle,
    textFirst: textFirstStyle,
    transparent: transparentStyle,
  }"
  :text="text"
  >
    <AddIcon size="200" />
</ButtonIcon>`,
      codeScript: `import ButtonIcon from 'totara_core/components/buttons/ButtonIcon;

export default {
  components: {
    ButtonIcon,
  }
}`,
    };
  },
};
</script>
