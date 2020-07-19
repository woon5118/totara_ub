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
    Available common button components

    <SamplesExample>
      <Table :data="iconData" :expandable-rows="true">
        <template v-slot:header-row>
          <HeaderCell size="3">Button</HeaderCell>
          <HeaderCell size="3">Name</HeaderCell>
          <HeaderCell size="10">Description</HeaderCell>
        </template>
        <template v-slot:row="{ row }">
          <Cell size="3" column-header="Button">
            <component :is="row.button" :size="size" @click="clicked()" />
          </Cell>

          <Cell size="3" column-header="Name">
            {{ row.name }}
          </Cell>

          <Cell size="10" column-header="Description">
            {{ row.desc }}
          </Cell>
        </template>
      </Table>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Size">
        <RadioGroup v-model="size" :horizontal="true">
          <Radio :value="100">100</Radio>
          <Radio :value="200">200</Radio>
          <Radio :value="300">300</Radio>
          <Radio :value="400">400</Radio>
          <Radio :value="500">500</Radio>
          <Radio :value="600">600</Radio>
          <Radio :value="700">700</Radio>
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
import CloseButton from 'totara_core/components/buttons/CloseIcon';
import MoreButton from 'totara_core/components/buttons/MoreIcon';

import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    Cell,
    FormRow,
    HeaderCell,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,
    Table,
  },

  data() {
    return {
      iconData: [
        {
          button: CloseButton,
          name: 'CloseButton',
          desc: '...',
        },
        {
          button: MoreButton,
          name: 'MoreButton',
          desc: '...',
        },
      ],
      size: 200,
      codeTemplate: `<CloseButton
  :aria-label="'String'"
  :size="size"
  @click="clicked()"
/>`,
      codeScript: `import CloseButton from 'totara_core/components/icons/common/CloseButton';

export default {
  components: {
    CloseButton,
  },
}`,
    };
  },

  methods: {
    clicked() {
      console.log('clicked');
    },
  },
};
</script>

<lang-strings>
{
  "moodle": ["more"]
}
</lang-strings>
