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
      <Table
        :color-odd-rows="colorOddRows"
        :data="dummyData"
        :expandable-rows="true"
        :border-bottom-hidden="hideBottomBorder"
        :border-top-hidden="hideTopBorder"
      >
        <template v-slot:header-row>
          <ExpandCell :header="true" />
          <HeaderCell size="16" valign="center">col 1</HeaderCell>
        </template>

        <template v-slot:row="{ row, expand, expandState }">
          <ExpandCell
            :aria-label="row.title"
            :expand-state="expandState"
            @click="expand()"
          />
          <Cell size="16" column-header="col 1" valign="center">
            {{ row.title }}
          </Cell>
        </template>

        <template v-slot:expand-content="{ row }">
          <h3>{{ row.title }}</h3>
          Expanded row content
        </template>
      </Table>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Colour odd rows">
        <RadioGroup v-model="colorOddRows" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Hide top border">
        <RadioGroup v-model="hideTopBorder" :horizontal="true">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Hide bottom border">
        <RadioGroup v-model="hideBottomBorder" :horizontal="true">
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
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Table from 'totara_core/components/datatable/Table';

import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';

export default {
  components: {
    Cell,
    ExpandCell,
    HeaderCell,
    Table,

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
          title: 'aaa',
        },
        {
          ready: true,
          title: 'some random text',
        },
        {
          ready: false,
          title: 'ccc',
        },
        {
          ready: true,
          title: 'ddd',
        },
      ],
      colorOddRows: false,
      hideBottomBorder: false,
      hideTopBorder: false,
      codeTemplate: `<Table
  :color-odd-rows="true"
  :data="dummyData"
  :expandable-rows="true"
>
  <!-- Header content -->
  <template v-slot:header-row>
    <ExpandCell :header="true" />
    <HeaderCell size="16" valign="center">col 1</HeaderCell>
  </template>

  <!-- Rows -->
  <template v-slot:row="{ row, expand, expandState }">
    <ExpandCell :expand-state="expandState" @click="expand()" />
    <Cell size="16" column-header="col 1" valign="center">
      {{ row.title }}
    </Cell>
  </template>

  <!-- Expand content -->
  <template v-slot:expand-content="{ row }">
    <h3>{{ row.title }}</h3>
  </template>
</Table>`,
      codeScript: `import Cell from 'totara_core/components/datatable/Cell';
import ExpandCell from 'totara_core/components/datatable/ExpandCell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    Cell,
    ExpandCell,
    HeaderCell,
    Table,
  },

  data() {
    return {
      dummyData: [
        {
          title: 'aaa',
        },
        {
          title: 'some random text',
        },
      ],
    }
  }
}`,
    };
  },
};
</script>
