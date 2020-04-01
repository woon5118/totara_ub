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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package totara_competency
-->

<template>
  <div>
    <h3>
      {{ $str('competencies', 'totara_competency', totalCompetencyCount) }}
    </h3>

    <SelectTable
      :value="value"
      :color-odd-rows="colorOddRows"
      :data="competencies"
      :expandable-rows="true"
      :select-all-enabled="true"
      @input="$emit('input', $event)"
    >
      <template v-slot:header-row>
        <HeaderCell size="16">{{
          $str('header_competency', 'totara_competency')
          }}</HeaderCell>
        <HeaderCell size="16">{{
          $str('header_assignment_status', 'totara_competency')
        }}</HeaderCell>
        <HeaderCell size="16">{{
          $str('header_assignment_reasons', 'totara_competency')
        }}</HeaderCell>
      </template>

      <template v-slot:row="{ row, expand }">
        <Cell
          size="16"
          :column-header="$str('header_competency', 'totara_competency')"
        >
          <a v-if="row.description" href="#" @click.prevent="expand()">{{
            row.display_name
          }}</a>
          <template v-else>{{ row.display_name }}</template>
        </Cell>
        <Cell
          size="16"
          :column-header="$str('header_assignment_status', 'totara_competency')"
        >
          {{ getAssignmentStatus(row) }}
        </Cell>
        <Cell
          size="16"
          :column-header="
            row.user_assignments.length > 0
              ? $str('header_assignment_reasons', 'totara_competency')
              : ''
          "
        >
          <ul class="tui-competencySelfAssignment__table-reasons">
            <li
              v-for="assignments in row.user_assignments"
              :key="assignments.id"
            >
              {{ assignments.reason_assigned }}
            </li>
          </ul>
        </Cell>
      </template>

      <template v-slot:expand-content="{ row }">
        <h3>{{ row.display_name }}</h3>
        <h4>{{ $str('description', 'totara_competency') }}</h4>
        <div v-html="row.description" />
      </template>
    </SelectTable>
  </div>
</template>

<script>
  import Cell from 'totara_core/components/datatable/Cell';
  import HeaderCell from 'totara_core/components/datatable/HeaderCell';
  import SelectTable from 'totara_core/components/datatable/SelectTable';

  export default {
    components: {
      Cell,
      HeaderCell,
      SelectTable,
    },
    props: {
      value: {
        type: Array,
        required: true,
      },
    competencies: {
      type: Array,
      required: true,
    },
    totalCompetencyCount: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      colorOddRows: false,
      page: 0,
    };
  },
  methods: {
    getAssignmentStatus(competency) {
      if (this.isAssigned(competency)) {
        return this.$str('currently_assigned', 'totara_competency');
      }

      return this.$str('not_assigned', 'totara_competency');
    },
    isAssigned(competency) {
      return (
        Boolean(competency.user_assignments) &&
        competency.user_assignments.length > 0
      );
    },
  },
};
</script>

<lang-strings>
  {
    "totara_competency": [
      "all",
      "competencies",
      "currently_assigned",
      "description",
      "competencies",
      "header_competency",
      "header_assignment_status",
      "header_assignment_reasons",
      "not_assigned"
    ]
  }
</lang-strings>
