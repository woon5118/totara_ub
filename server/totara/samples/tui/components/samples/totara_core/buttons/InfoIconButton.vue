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
  <div class="tui-testButton">
    The button icon component is used when triggering an on page action.

    <SamplesExample>
      <InfoIconButton :aria-label="'Add'" :disabled="disabled">
        Some content here...
      </InfoIconButton>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ labelId }" label="Disabled">
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
// Components
import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    InfoIconButton,
    FormRow,
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
      smallStyle: false,
      text: '',
      codeTemplate: `<InfoIconButton :aria-label="'Add'" :disabled="disabled">
  Some content here...
</InfoIconButton>`,
      codeScript: `import InfoIconButton from 'totara_core/components/buttons/InfoIconButton';

export default {
  components: {
    InfoIconButton,
  }
}
      `,
    };
  },
};
</script>
