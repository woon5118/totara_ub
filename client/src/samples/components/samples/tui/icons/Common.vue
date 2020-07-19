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
  @module samples
-->

<template>
  <div>
    Available Icon components

    <SamplesExample>
      <Table :data="iconData" :expandable-rows="true">
        <template v-slot:header-row>
          <HeaderCell size="3">Icon</HeaderCell>
          <HeaderCell size="3">Name</HeaderCell>
          <HeaderCell size="10">Description</HeaderCell>
        </template>
        <template v-slot:row="{ row, expand }">
          <Cell size="3">
            <component :is="row.icon" :size="size" />
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
          <Radio value="100">100</Radio>
          <Radio value="200">200</Radio>
          <Radio value="300">300</Radio>
          <Radio value="400">400</Radio>
          <Radio value="500">500</Radio>
          <Radio value="600">600</Radio>
          <Radio value="700">700</Radio>
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
import Add from 'tui/components/icons/common/Add';
import AddUser from 'tui/components/icons/common/AddUser';
import Archive from 'tui/components/icons/common/Archive';
import BackArrow from 'tui/components/icons/common/BackArrow';
import ForwardArrow from 'tui/components/icons/common/ForwardArrow';
import CheckSuccess from 'tui/components/icons/common/CheckSuccess';
import Close from 'tui/components/icons/common/Close';
import Comment from 'tui/components/icons/common/Comment';
import Delete from 'tui/components/icons/common/Delete';
import Edit from 'tui/components/icons/common/Edit';
import Error from 'tui/components/icons/common/Error';
import Collapse from 'tui/components/icons/common/Collapse';
import Expand from 'tui/components/icons/common/Expand';
import More from 'tui/components/icons/common/More';
import RemoveUser from 'tui/components/icons/common/RemoveUser';
import Remove from 'tui/components/icons/common/Remove';
import Search from 'tui/components/icons/common/Search';
import Slider from 'tui/components/icons/common/Slider';
import Success from 'tui/components/icons/common/Success';
import Show from 'tui/components/icons/common/Show';
import Hide from 'tui/components/icons/common/Hide';
import List from 'tui/components/icons/common/List';
import Grid from 'tui/components/icons/common/Grid';
import Info from 'tui/components/icons/common/Info';
import Spinner from 'tui/components/icons/common/Spinner';
import Warning from 'tui/components/icons/common/Warning';

import Cell from 'tui/components/datatable/Cell';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import FormRow from 'tui/components/form/FormRow';
import Radio from 'tui/components/form/Radio';
import RadioGroup from 'tui/components/form/RadioGroup';
import SamplesCode from 'samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'samples/components/sample_parts/misc/SamplesPropCtl';
import Table from 'tui/components/datatable/Table';

export default {
  components: {
    Cell,
    HeaderCell,
    FormRow,
    ForwardArrow,
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
          icon: Add,
          name: 'Add',
          desc: '...',
        },
        {
          icon: AddUser,
          name: 'AddUser',
          desc: '...',
        },
        {
          icon: Archive,
          name: 'Archive',
          desc: '...',
        },
        {
          icon: BackArrow,
          name: 'GoBack',
          desc: 'Used for going back to previous page or place',
        },
        {
          icon: ForwardArrow,
          name: 'GoForward',
          desc: 'Used for going forward to next page or place',
        },
        {
          icon: CheckSuccess,
          name: 'CheckSuccess',
          desc: '...',
        },
        {
          icon: Close,
          name: 'Close',
          desc: '...',
        },
        {
          icon: Collapse,
          name: 'Collapse',
          desc: 'Used for collapsing additional content',
        },
        {
          icon: Comment,
          name: 'Comment',
          desc: '...',
        },
        {
          icon: Delete,
          name: 'Delete',
          desc: '...',
        },
        {
          icon: Edit,
          name: 'Edit',
          desc: '...',
        },
        {
          icon: Error,
          name: 'Error',
          desc: '...',
        },
        {
          icon: Expand,
          name: 'Expand',
          desc: 'Used for expanding additional content',
        },
        {
          icon: Grid,
          name: 'Grid',
          desc: '...',
        },
        {
          icon: Hide,
          name: 'Hide',
          desc: 'Used for collasping form like elements',
        },
        {
          icon: Info,
          name: 'Info',
          desc: '...',
        },
        {
          icon: List,
          name: 'List',
          desc: '...',
        },
        {
          icon: More,
          name: 'More',
          desc: '...',
        },
        {
          icon: Remove,
          name: 'Remove',
          desc: '...',
        },
        {
          icon: RemoveUser,
          name: 'RemoveUser',
          desc: '...',
        },
        {
          icon: Search,
          name: 'Search',
          desc: '...',
        },
        {
          icon: Show,
          name: 'Show',
          desc: 'Used for expanding form like elements',
        },
        {
          icon: Slider,
          name: 'Slider',
          desc: '...',
        },
        {
          icon: Spinner,
          name: 'Spinner',
          desc: '...',
        },
        {
          icon: Success,
          name: 'Success',
          desc: '...',
        },
        {
          icon: Warning,
          name: 'Warning',
          desc: '...',
        },
      ],
      size: '200',
      codeTemplate: `<Close :size="size"/>`,
      codeScript: `import Close from 'tui/components/icons/common/Close';

export default {
  components: {
    Close,
  },
}`,
    };
  },
};
</script>
