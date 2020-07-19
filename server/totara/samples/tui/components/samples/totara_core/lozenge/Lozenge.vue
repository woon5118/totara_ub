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
  <div class="tui-loader">
    A visual UI component

    <SamplesExample>
      <Lozenge :text="text" :type="type" />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ labelId }" label="Type">
        <RadioGroup v-model="type" :horizontal="true">
          <Radio value="alert">Alert</Radio>
          <Radio value="info">Info</Radio>
          <Radio value="success">Success</Radio>
          <Radio value="warning">Warning</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ id, label }" label="Text">
        <InputText :id="id" v-model="text" :placeholder="text" />
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
import Lozenge from 'totara_core/components/lozenge/Lozenge';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    FormRow,
    InputText,
    Lozenge,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      text: 'Danger',
      type: 'alert',
      codeTemplate: `<Lozenge :text="text" :type="type" />`,
      codeScript: `import Lozenge from 'totara_core/components/lozenge/Lozenge';

export default {
  components: {
    Lozenge,
  }
}`,
    };
  },
};
</script>
