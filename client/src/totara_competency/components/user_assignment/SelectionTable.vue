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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module totara_competency
-->

<template>
  <div>
    <h3 class="tui-competencySelfAssignment__header">
      {{ $str('competencies', 'totara_competency', totalCompetencyCount) }}
    </h3>

    <SelectTable
      :value="value"
      :data="competencies"
      :expandable-rows="true"
      :select-all-enabled="true"
      @input="$emit('input', $event)"
    >
      <template v-slot:header-row>
        <ExpandCell :header="true" />
        <HeaderCell size="4">
          {{ $str('header_competency', 'totara_competency') }}
        </HeaderCell>
        <HeaderCell size="4">
          {{ $str('header_assignment_status', 'totara_competency') }}
        </HeaderCell>
        <HeaderCell size="4">
          {{ $str('header_assignment_reasons', 'totara_competency') }}
        </HeaderCell>
      </template>

      <template v-slot:row="{ row, expand, expandState }">
        <ExpandCell
          :empty="!row.description"
          :expand-state="expandState"
          @click="expand()"
        />
        <Cell
          size="4"
          :column-header="$str('header_competency', 'totara_competency')"
        >
          {{ row.display_name }}
        </Cell>
        <Cell
          size="4"
          :column-header="$str('header_assignment_status', 'totara_competency')"
        >
          {{ getAssignmentStatus(row) }}
        </Cell>
        <Cell
          size="4"
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
        <div class="tui-competencySelfAssignment__tableExpand">
          <h4 class="tui-competencySelfAssignment__tableExpand-header">
            {{ row.display_name }}
          </h4>
          <h5 class="tui-competencySelfAssignment__tableExpand-subHeader">
            {{ $str('description', 'totara_competency') }}
          </h5>
          <div v-html="row.description" />
        </div>
      </template>
    </SelectTable>
  </div>
</template>

<script>
import Cell from 'tui/components/datatable/Cell';
import ExpandCell from 'tui/components/datatable/ExpandCell';
import HeaderCell from 'tui/components/datatable/HeaderCell';
import SelectTable from 'tui/components/datatable/SelectTable';

export default {
  components: {
    Cell,
    ExpandCell,
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
