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
