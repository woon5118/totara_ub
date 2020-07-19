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
    collapsible component for expanding or collapsing content

    <SamplesExample>
      <Collapsible :label="label" :initial-state="expanded">
        {{ content }}
      </Collapsible>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ id }" label="Label">
        <InputText :id="id" v-model="label" />
      </FormRow>
      <FormRow v-slot="{ id }" label="Content">
        <Textarea :id="id" v-model="content" :rows="4" />
      </FormRow>
      <h4 class="tui-samplesCtl__optional">Optional</h4>
      <FormRow label="InitialState">
        <RadioGroup v-model="expanded" :horizontal="true">
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
import Collapsible from 'totara_core/components/collapsible/Collapsible';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import Textarea from 'totara_core/components/form/Textarea';

export default {
  components: {
    Collapsible,
    FormRow,
    InputText,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
    Textarea,
  },

  data() {
    return {
      content: '...',
      expanded: false,
      label: 'Heading',

      codeTemplate: `<Collapsible :label="label" :initial-state="false">
  {{ content }}
</Collapsible>`,
      codeScript: `import collapsible from 'totara_core/components/collapsible/Collapsible;

export default {
  components: {
    Collapsible,
  }
  data() {
    return {
      expanded: false,
      label: 'Heading',
    }
  }
}`,
    };
  },
};
</script>
