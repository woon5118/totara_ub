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
    The ToggleButtonIcon component is included within a ToggleSet and used for
    switching between two states.

    <SamplesExample>
      <ToggleSet v-model="selection" :disabled="disabled" @input="toggleChange">
        <ToggleButtonIcon value="left" label="left label">
          <GridIcon />
        </ToggleButtonIcon>
        <ToggleButtonIcon value="right" label="right label">
          <ListIcon />
        </ToggleButtonIcon>
      </ToggleSet>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ labelId }" label="Disabled">
        <RadioGroup v-model="disabled" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>
      <FormRow v-slot="{ labelId }" label="Selected">
        <RadioGroup v-model="selection" :horizontal="true">
          <Radio value="left">Left</Radio>
          <Radio value="right">Right</Radio>
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
import ListIcon from 'totara_core/components/icons/common/List';
import GridIcon from 'totara_core/components/icons/common/Grid';
import ToggleSet from 'totara_core/components/buttons/ToggleSet';
import ToggleButtonIcon from 'totara_core/components/buttons/ToggleButtonIcon';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

export default {
  components: {
    ListIcon,
    GridIcon,
    ToggleSet,
    ToggleButtonIcon,
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
  },

  data() {
    return {
      selection: '',
      disabled: false,

      codeTemplate: `<ToggleSet v-model="selection" :disabled="disabled" @input="toggleChange">
  <ToggleButtonIcon value="left" label="left label">
    <GridIcon />
  </ToggleButtonIcon>
  <ToggleButtonIcon value="right" label="right label">
    <ListIcon />
  </ToggleButtonIcon>
</ToggleSet>`,
      codeScript: `import ToggleSet from 'totara_core/components/buttons/ToggleSet';
import ToggleButtonIcon from 'totara_core/components/buttons/ToggleButtonIcon';

export default {
  components: {
    ToggleSet,
    ToggleButtonIcon
  }
}`,
    };
  },

  methods: {
    /**
     * Toggle state change
     *
     * @param {string} value
     */
    toggleChange(value) {
      console.log('toggle state changed to ' + value);
    },
  },
};
</script>
