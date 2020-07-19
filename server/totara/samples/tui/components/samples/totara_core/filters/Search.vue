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
    <SamplesExample>
      <SearchFilter
        v-model="searchFilter"
        label="Filter items"
        :disabled="disabled"
        :placeholder="'Filtering'"
        :show-label="showLabel"
        :stacked="stacked"
        @submit="searchSubmit"
      />
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Stacked">
        <RadioGroup v-model="stacked" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Show label">
        <RadioGroup v-model="showLabel" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Disabled">
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
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';

// Components
import SearchFilter from 'totara_core/components/filters/SearchFilter';

export default {
  components: {
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
    SearchFilter,
  },

  data() {
    return {
      disabled: false,
      searchFilter: '',
      showLabel: true,
      stacked: false,
      codeTemplate: `<SearchFilter
  v-model="searchFilter"
  label="Filter items"
  :disabled="disabled"
  :placeholder="'Filtering'"
  :show-label="showLabel"
  :stacked="stacked"
  @submit="searchSubmit"
/>`,
      codeScript: `import SearchFilter from 'totara_core/components/filters/SearchFilter';

export default {
  components: {
    SearchFilter,
  }
}`,
    };
  },

  methods: {
    searchSubmit() {
      console.log('submitted');
    },
  },
};
</script>
