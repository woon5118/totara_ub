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
  <div class="tui-competencyDetailActivityLog">
    <h4 class="tui-competencyDetailActivityLog__title">
      {{ $str('activity_log', 'totara_competency') }}
    </h4>

    <FilterBar
      v-if="assignments.length > 1"
      :title="$str('activity_log_filter', 'totara_competency')"
    >
      <template v-slot:filters-left>
        <SelectFilter
          v-model="assignmentFilter"
          :label="$str('assignment', 'totara_competency')"
          :options="assignments"
          :show-label="true"
          :stacked="false"
        />
      </template>
    </FilterBar>

    <Loader :loading="$apollo.loading">
      <ActivityLogTable :log="log" />
    </Loader>
  </div>
</template>

<script>
// Components
import ActivityLogTable from 'totara_competency/components/details/ActivityLogTable';
import FilterBar from 'totara_core/components/filters/FilterBar';
import Loader from 'totara_core/components/loader/Loader';
import SelectFilter from 'totara_core/components/filters/SelectFilter';
// GraphQL
import ActivityLogQuery from 'totara_competency/graphql/activity_log';

export default {
  components: {
    ActivityLogTable,
    FilterBar,
    Loader,
    SelectFilter,
  },
  props: {
    userId: {
      required: true,
      type: Number,
    },
    competencyId: {
      required: true,
      type: Number,
    },
  },

  data: function() {
    return {
      log: [],
      assignments: [],
      assignmentFilter: 0,
    };
  },

  /**
   * Fetch the activity log content
   *
   */
  apollo: {
    log: {
      query: ActivityLogQuery,
      variables() {
        return {
          user_id: this.userId,
          competency_id: this.competencyId,
          filters: this.selectedFilter,
        };
      },
      update({ totara_competency_activity_log: log }) {
        this.assignments = this.refreshAssignmentList(log);
        return log;
      },
    },
  },

  computed: {
    /**
     * Provided filter value in correct format for apollo
     *
     * @return {Object}
     */
    selectedFilter() {
      if (this.assignmentFilter) {
        return { assignment_id: this.assignmentFilter };
      }
      return {};
    },
  },

  methods: {
    /**
     * Create an array of assignments to be used for the filter
     *
     * @return {Array}
     */
    refreshAssignmentList(log) {
      let assignmentList = {};

      log.forEach(entry => {
        if (entry.assignment != null) {
          assignmentList[entry.assignment.id] = {
            id: entry.assignment.id,
            label: this.getFilterLabel(entry),
          };
        }
      });

      assignmentList = Object.values(assignmentList);
      if (assignmentList.length >= this.assignments.length) {
        // Add placeholder 'all' option
        assignmentList.unshift({
          label: this.$str('all', 'moodle'),
          id: 0,
        });
        return assignmentList;
      } else {
        return this.assignments;
      }
    },

    /**
     * Modify the assignment label if it was directly assigned
     *
     * @param {Object} entry
     * @return {String}
     */
    getFilterLabel(entry) {
      let label = entry.assignment.progress_name;
      // Special handling for direct assignments which we list
      // as "Directly assigned", this adds some more information
      // to the string which includes the full name of the assigner and their role
      if (this.isDirectlyAssigned(entry.assignment)) {
        let str_options = {
          progress_name: entry.assignment.progress_name,
          user_fullname_role: entry.assignment.reason_assigned,
        };
        label = this.$str(
          'progress_name_by_user',
          'totara_competency',
          str_options
        );
      }

      return label;
    },

    /**
     * Calculate if label was directly assigned
     *
     * @param {Object} assignment
     * @return {Boolean}
     */
    isDirectlyAssigned(assignment) {
      if (assignment.user_group_type !== 'user') {
        return false;
      }

      return assignment.type === 'admin' || assignment.type === 'other';
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "all"
    ],
    "totara_competency": [
      "activity_log",
      "activity_log_filter",
      "assignment",
      "progress_name_by_user"
    ]
  }
</lang-strings>
