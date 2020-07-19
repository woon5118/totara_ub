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
  <div class="tui-loader">
    A visual UI card component

    <SamplesExample>
      <Card :clickable="clickable" @click="cardClicked">
        <div>
          <p class="tui-samples--highlight">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
            eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem
            ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum
            dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
            incididunt ut labore et dolore magna aliqua.
          </p>
          <p class="tui-samples--highlight">
            Cursus euismod quis viverra nibh cras. Lorem ipsum dolor sit amet,
            consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
            labore et dolore magna aliqua.
          </p>
        </div>
      </Card>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Clickable">
        <RadioGroup v-model="clickable" :horizontal="true">
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
import Card from 'totara_core/components/card/Card';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Card,
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      clickable: false,
      codeTemplate: `<Card :clickable="clickable" @click="cardClicked">
  <div>
    Card content
  </div>
</Card>`,
      codeScript: `import Card from 'totara_core/components/card/Card';

export default {
  components: {
    Card,
  },

  methods: {
    cardClicked() {
      console.log('clicked');
    },
  },
}`,
    };
  },

  methods: {
    cardClicked() {
      console.log('clicked');
    },
  },
};
</script>
