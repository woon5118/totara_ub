<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    A notification banner is usually displayed at the top of the page content
    and added on page load.

    <SamplesExample>
      <NotificationBanner
        :dismissable="dismissable"
        :message="message"
        :type="type"
        :self-dismiss="selfDismiss"
        @dismiss="dismiss"
      />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Type">
        <RadioGroup v-model="type" :horizontal="true">
          <Radio value="info">Info</Radio>
          <Radio value="success">Success</Radio>
          <Radio value="warning">Warning</Radio>
          <Radio value="error">Error</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Message">
        <InputText :id="id" v-model="message" :placeholder="label" />
      </FormRow>

      <FormRow label="Dismissable">
        <RadioGroup v-model="dismissable" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Self-dismiss">
        <RadioGroup v-model="selfDismiss" :horizontal="true">
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
import NotificationBanner from 'totara_core/components/notifications/NotificationBanner';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import FormRow from 'totara_core/components/form/FormRow';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import Radio from 'totara_core/components/form/Radio';
import InputText from 'totara_core/components/form/InputText';

export default {
  components: {
    NotificationBanner,
    SamplesExample,
    SamplesPropCtl,
    SamplesCode,
    FormRow,
    RadioGroup,
    Radio,
    InputText,
  },

  data() {
    return {
      type: 'info',
      message: 'Every time you lick a stamp, you consume 1/10 of a calorie.',
      dismissable: false,
      selfDismiss: false,
      codeTemplate: `<NotificationBanner type="info" message="Message."/>`,
      codeScript: `import Loader from 'totara_core/components/notifications/NotificationBanner;

export default {
  components: {
    NotificationBanner,
  },
};`,
    };
  },

  methods: {
    dismiss() {
      console.log('notification dismissed');
    },
  },
};
</script>
