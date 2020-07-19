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
