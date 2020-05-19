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
import Add from 'totara_core/components/icons/common/Add';
import AddUser from 'totara_core/components/icons/common/AddUser';
import Archive from 'totara_core/components/icons/common/Archive';
import BackArrow from 'totara_core/components/icons/common/BackArrow';
import ForwardArrow from 'totara_core/components/icons/common/ForwardArrow';
import CheckSuccess from 'totara_core/components/icons/common/CheckSuccess';
import Close from 'totara_core/components/icons/common/Close';
import Comment from 'totara_core/components/icons/common/Comment';
import Delete from 'totara_core/components/icons/common/Delete';
import Edit from 'totara_core/components/icons/common/Edit';
import Error from 'totara_core/components/icons/common/Error';
import Collapse from 'totara_core/components/icons/common/Collapse';
import Expand from 'totara_core/components/icons/common/Expand';
import More from 'totara_core/components/icons/common/More';
import RemoveUser from 'totara_core/components/icons/common/RemoveUser';
import Remove from 'totara_core/components/icons/common/Remove';
import Search from 'totara_core/components/icons/common/Search';
import Slider from 'totara_core/components/icons/common/Slider';
import Success from 'totara_core/components/icons/common/Success';
import Show from 'totara_core/components/icons/common/Show';
import Hide from 'totara_core/components/icons/common/Hide';
import List from 'totara_core/components/icons/common/List';
import Grid from 'totara_core/components/icons/common/Grid';
import Info from 'totara_core/components/icons/common/Info';
import Spinner from 'totara_core/components/icons/common/Spinner';
import Warning from 'totara_core/components/icons/common/Warning';

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
      codeScript: `import Close from 'totara_core/components/icons/common/Close';

export default {
  components: {
    Close,
  },
}`,
    };
  },
};
</script>
