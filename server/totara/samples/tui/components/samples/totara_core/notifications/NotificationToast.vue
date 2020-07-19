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
    A toast notification used for providing the user instant feedback on the
    result of an action or query.

    <SamplesExample>
      <Button text="Trigger Notification" @click="triggerNotification" />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ labelId }" label="Type">
        <RadioGroup v-model="type" :horizontal="true">
          <Radio value="info">Info</Radio>
          <Radio value="success">Success</Radio>
          <Radio value="warning">Warning</Radio>
          <Radio value="error">Error</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ labelId }" label="Duration">
        <RadioGroup v-model="duration" :horizontal="true">
          <Radio value="500">0.5 seconds</Radio>
          <Radio value="5000">5 seconds</Radio>
          <Radio value="10000">10 seconds</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Message">
        <InputText :id="id" v-model="message" :placeholder="label" />
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import { notify } from 'totara_core/notifications';
import Button from 'totara_core/components/buttons/Button';
import FormRow from 'totara_core/components/form/FormRow';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    Button,
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
      type: 'info',
      duration: 5000,
      message: 'bla bla bla',
      codeTemplate: `// Example Trigger
<Button text="Trigger Notification" @click="triggerNotification" />`,
      codeScript: `import Button from 'totara_core/components/buttons/Button';
import { notify } from 'totara_core/notifications';

export default {
  components: {
    Button,
  }

  methods: {
    triggerNotification() {
      notify({
        duration: this.duration,
        message: this.message,
        type: this.type,
      });
    },
  },
}`,
    };
  },

  methods: {
    triggerNotification() {
      notify({
        duration: this.duration,
        message: this.message,
        type: this.type,
      });
    },
  },
};
</script>
