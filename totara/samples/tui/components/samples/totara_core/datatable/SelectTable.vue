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
      <SelectTable
        v-model="selection"
        :data="dummyData"
        :color-odd-rows="colorOddRows"
        :checkbox-v-align="checkboxVAlign"
        :select-all-enabled="selectAllEnabled"
        :select-entire-enabled="selectEntireEnabled"
        :entire-selected="entireSelected"
        row-label-key="display_name"
      >
        <template v-slot:header-row>
          <HeaderCell size="12" valign="center">Col 1</HeaderCell>
          <HeaderCell size="4" valign="center">Col 2</HeaderCell>
        </template>

        <template v-slot:row="{ row }">
          <Cell size="12" :column-header="'Col 1'" valign="center">
            {{ row.display_name }}
          </Cell>

          <Cell size="4" :column-header="'Col 2'" valign="center">
            {{ row.ready }}
          </Cell>
        </template>
      </SelectTable>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="value">
        <RadioGroup v-model="selection" :horizontal="true">
          <Radio :value="[]">[]</Radio>
          <Radio :value="[1, 3]">[1, 3]</Radio>
          <Radio :value="[0, 1, 2, 3]">[0, 1, 2, 3]</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="color-odd-rows">
        <RadioGroup v-model="colorOddRows" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <h4 class="tui-samplesCtl__optional">Selection (Optional)</h4>

      <FormRow label="checkbox-v-align">
        <RadioGroup v-model="checkboxVAlign" :horizontal="true">
          <Radio value="start">Start</Radio>
          <Radio value="center">Center</Radio>
          <Radio value="end">End</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="select-all-enabled">
        <RadioGroup v-model="selectAllEnabled" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="select-entire-enabled">
        <RadioGroup v-model="selectEntireEnabled" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="entire-selected">
        <RadioGroup v-model="entireSelected" :horizontal="true">
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
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import SelectTable from 'totara_core/components/datatable/SelectTable';

import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';

export default {
  components: {
    Cell,
    HeaderCell,
    SelectTable,

    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
    FormRow,
    Radio,
    RadioGroup,
  },

  data() {
    return {
      dummyData: [
        {
          ready: true,
          display_name: 'aaa',
        },
        {
          ready: true,
          display_name: 'Some example text',
        },
        {
          ready: false,
          display_name: 'ccc',
        },
        {
          ready: true,
          display_name: 'ddd',
        },
      ],
      selection: [1, 3],

      checkboxVAlign: 'center',
      colorOddRows: false,
      selectAllEnabled: false,
      selectEntireEnabled: false,
      entireSelected: false,

      codeTemplate: `<SelectTable
  v-model="selection"
  :data="data"
  :color-odd-rows="true"
  checkbox-v-align="center"
  :select-all-enabled="true"
  :select-entire-enabled="false"
  :entire-selected="false"
>
  <!-- Header content -->
  <template v-slot:header-row>
    <HeaderCell size="12" valign="center">Col 1</HeaderCell>
    <HeaderCell size="4" valign="center">Col 2</HeaderCell>
  </template>

  <!-- Rows -->
  <template v-slot:row="{ row }">
    <Cell size="12" :column-header="'Col 1'" valign="center">
      {{ row.title }}
    </Cell>

    <Cell size="4" :column-header="'Col 2'" valign="center">
      {{ row.ready }}
    </Cell>
  </template>
</SelectTable>`,
      codeScript: `import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import SelectTable from 'totara_core/components/datatable/SelectTable';

export default {
  components: {
    Cell,
    HeaderCell,
    SelectTable,
  },


  data() {
    return {
      data: [
        {
          ready: 'true',
          title: 'aaa',
        },
        {
          ready: 'false',
          title: 'some random text',
        },
      ],
      selection: [1, 3],
    }
  }
}`,
    };
  },
};
</script>
