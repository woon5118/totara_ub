<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @module totara_competency
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
              row.proficient_status != null,
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
        <ProficientStatus
          v-if="row.proficient_status != null"
          :proficient-status="row.proficient_status"
        />
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
import AddUserIcon from 'tui/components/icons/common/AddUser';
import Cell from 'tui/components/datatable/Cell';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import Popover from 'tui/components/popover/Popover';
import ProficientStatus from 'totara_competency/components/details/ProficientStatus';
import RemoveUserIcon from 'tui/components/icons/common/RemoveUser';
import Table from 'tui/components/datatable/Table';

export default {
  components: {
    AddUserIcon,
    Cell,
    HeaderCell,
    Popover,
    ProficientStatus,
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
      "progress_name_by_user"
    ]
  }
</lang-strings>
