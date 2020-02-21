<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_competency
-->

<template>
  <Table :data="log">
    <template v-slot:header-row>
      <HeaderCell size="2">
        {{ $str('date', 'moodle') }}
      </HeaderCell>
      <HeaderCell size="8">
        {{ $str('description', 'moodle') }}
      </HeaderCell>
      <HeaderCell size="3">
        {{ $str('proficiency_status', 'totara_competency') }}
      </HeaderCell>
      <HeaderCell size="3">
        {{ $str('assignment', 'totara_competency') }}
      </HeaderCell>
    </template>
    <template v-slot:row="{ row, expand }">
      <!-- Date column -->
      <Cell size="2" :column-header="$str('date', 'moodle')">
        <Popover :triggers="['hover']">
          <template v-slot:trigger>
            <span class="tui-competencyDetailActivityLog__date">
              {{ row.date }}
            </span>
          </template>
          <div>{{ row.datetime }}</div>
        </Popover>
      </Cell>

      <!-- Description column -->
      <Cell
        size="8"
        :column-header="$str('description', 'moodle')"
        class="tui-competencyDetailActivityLog__description"
      >
        <AddUserIcon v-if="row.assignment_action === 'assigned'" size="200" />
        <RemoveUserIcon
          v-if="
            row.assignment_action === 'unassigned_archived' ||
              row.assignment_action === 'unassigned_usergroup'
          "
          size="200"
        />
        <span
          :class="{
            'tui-competencyDetailActivityLog__description-rating':
              row.proficient_status,
            'tui-competencyDetailActivityLog__description-system':
              row.assignment && row.assignment.type === 'system',
            'tui-competencyDetailActivityLog__description-tracking':
              row.assignment_action &&
              (row.assignment_action === 'tracking_started' ||
                row.assignment_action === 'tracking_ended'),
          }"
        >
          {{ row.description }}
        </span>
      </Cell>

      <!-- Proficient column -->
      <Cell
        size="3"
        :column-header="$str('proficiency_status', 'totara_competency')"
      >
        <span
          class="tui-competencyDetailActivityLog__proficient"
          :class="{
            'tui-competencyDetailActivityLog__proficient-srOnly': !row.proficient_status,
          }"
        >
          <ProficientIcon v-if="row.proficient_status" size="200" />
          <span>
            {{
              $str(
                row.proficient_status ? 'proficient' : 'not_proficient',
                'totara_competency'
              )
            }}
          </span>
        </span>
      </Cell>

      <!-- Assignment column -->
      <Cell size="3" :column-header="$str('assignment', 'totara_competency')">
        <span v-if="showProgressName(row)">
          {{ row.assignment.progress_name }}
        </span>
      </Cell>
    </template>
  </Table>
</template>

<script>
import AddUserIcon from 'totara_core/components/icons/common/AddUser';
import Cell from 'totara_core/components/datatable/Cell';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import Popover from 'totara_core/components/popover/Popover';
import ProficientIcon from 'totara_core/components/icons/common/CheckSuccess';
import RemoveUserIcon from 'totara_core/components/icons/common/RemoveUser';
import Table from 'totara_core/components/datatable/Table';

export default {
  components: {
    AddUserIcon,
    Cell,
    HeaderCell,
    Popover,
    ProficientIcon,
    RemoveUserIcon,
    Table,
  },
  props: {
    log: {
      type: Array,
    },
  },

  methods: {
    showProgressName(data) {
      return (
        data.assignment != null &&
        !['tracking_started', 'tracking_ended'].includes(data.assignment_action)
      );
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "date",
      "description"
    ],
    "totara_competency": [
      "assignment",
      "proficiency_status",
      "progress_name_by_user",
      "not_proficient",
      "proficient"
    ]
  }
</lang-strings>
