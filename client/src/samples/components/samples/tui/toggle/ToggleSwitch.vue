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

  @author Steve Barnett <steve.barnett@totaralearning.com>
  @module totara_samples
-->

<template>
  <div>
    <p>
      This toggle switch is used to immediately update parts of the UI. It's an
      on/off switch for a state, and can be used as part of a form to reveal
      more related form elements, or used outside of a form to reveal non-form
      elements.
    </p>

    <p>
      On <code>click</code> we change only the button's
      (<code>aria-pressed</code>) state, not its label.
    </p>

    <SamplesExample>
      <ToggleSwitch
        id="toggle"
        v-model="isToggled"
        :text="text"
        :toggle-first="toggleFirst"
      >
        <template v-slot:icon>
          <InfoIconButton :aria-label="'Help'">
            Some content here...
          </InfoIconButton>
        </template>
      </ToggleSwitch>

      <br /><br />
      <ToggleSwitch
        id="toggle"
        v-model="isToggled"
        :text="text"
        :toggle-first="toggleFirst"
      />

      <br /><br />

      <ToggleSwitch
        id="toggle"
        v-model="isToggled"
        :text="text"
        :toggle-first="toggleFirst"
        :aria-label="ariaLabel"
      />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="toggleFirst">
        <RadioGroup v-model="toggleFirst" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="ariaLabel">
        <InputText v-model="ariaLabel" />
      </FormRow>
    </SamplesPropCtl>
  </div>
</template>

<script>
// Components
import FormRow from 'totara_core/components/form/FormRow';
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import ToggleSwitch from 'totara_core/components/toggle/ToggleSwitch';

export default {
  components: {
    FormRow,
    InfoIconButton,
    InputText,
    Radio,
    RadioGroup,
    SamplesExample,
    SamplesPropCtl,
    ToggleSwitch,
  },

  data() {
    return {
      text: 'Toggle something in the UI',
      toggleFirst: true,
      ariaLabel: 'The toggle button text',
      isToggled: false,
    };
  },
};
</script>
