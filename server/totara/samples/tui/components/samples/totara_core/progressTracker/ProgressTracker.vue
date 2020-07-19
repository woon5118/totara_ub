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
  <div class="tui-testProgressTracker">
    A progress track component for showing progress made towards achieving a
    goal.

    <SamplesExample>
      <ProgressTracker
        :items="items"
        :gap="gap"
        :current-id="currentId"
        :target-id="targetId"
      />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{}" label="Gap">
        <RadioGroup v-model="gap" :horizontal="true">
          <Radio value="small">Small</Radio>
          <Radio value="medium">Medium</Radio>
          <Radio value="large">Large</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{}" label="currentId">
        <RadioGroup v-model="currentId" :horizontal="true">
          <Radio :value="NaN">None</Radio>
          <Radio :value="1">1</Radio>
          <Radio :value="2">2</Radio>
          <Radio :value="3">3</Radio>
          <Radio :value="4">4</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{}" label="targetId">
        <RadioGroup v-model="targetId" :horizontal="true">
          <Radio :value="NaN">None</Radio>
          <Radio :value="1">1</Radio>
          <Radio :value="2">2</Radio>
          <Radio :value="3">3</Radio>
          <Radio :value="4">4</Radio>
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
import ProgressTracker from 'totara_core/components/progresstracker/ProgressTracker';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    ProgressTracker,
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      gap: 'medium',
      currentId: 1,
      targetId: 3,

      items: [
        {
          id: 1,
          description: 'Basic knowledge description',
          label: 'Basic knowledge',
        },
        {
          id: 2,
          description: 'Competent with supervision description',
          label: 'Competent with supervision',
        },
        {
          id: 3,
          description: 'Competent description',
          label: 'Competent',
        },
        {
          id: 4,
          description: 'Able to supervise others description',
          label: 'Able to supervise others',
        },
      ],

      codeTemplate: `<ProgressTracker
  :items="items"
  :gap="gap"
  :current-id="currentId"
  :target-id="targetId"
/>`,
      codeScript: `import ProgressTracker from 'totara_core/components/progresstracker/ProgressTracker';

export default {
  components: {
    ProgressTracker,
  }
}`,
    };
  },
};
</script>
