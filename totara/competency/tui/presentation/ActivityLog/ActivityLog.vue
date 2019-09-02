<template>
  <div>
    <h4 class="tui-ActivityLog__subtitle">
      {{ $str('activity_log', 'totara_competency') }}
    </h4>
    <AssignmentFilter
      v-if="hasMultipleAssignments"
      :assignments="assignments"
      @filter-updated="assignmentFilterUpdated"
    />
    <List :data="log" :columns="columns">
      <template v-slot:column-date="props">
        <Date :data="props.row" />
      </template>
      <template v-slot:column-description="props">
        <Description :data="props.row" />
      </template>
      <template v-slot:column-proficientStatus="props">
        <ProficientStatus
          v-if="props.row.proficient_status != null"
          :status="props.row.proficient_status"
        />
      </template>
      <template v-slot:column-assignment="props">
        <span v-if="showProgressName(props.row)">
          {{ props.row.assignment.progress_name }}
        </span>
      </template>
    </List>
  </div>
</template>

<script>
const ACTION_TRACKING_START = 'tracking_started';
const ACTION_TRACKING_END = 'tracking_ended';

import List from '../../container/List';
import Date from './Date';
import ProficientStatus from './ProficientStatus';
import Description from './Description';
import AssignmentFilter from './AssignmentFilter';

import ActivityLogQuery from '../../../webapi/ajax/activity_log.graphql';

export default {
  components: { Description, ProficientStatus, Date, List, AssignmentFilter },
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
      showTooltip: false,
      columns: [
        {
          key: 'date',
          title: this.$str('date', 'moodle'),
          size: 'xs',
        },
        {
          key: 'description',
          title: this.$str('description', 'moodle'),
          size: 'lg',
        },
        {
          key: 'proficientStatus',
          title: this.$str('activitylog_proficientstatus', 'totara_competency'),
          size: 'sm',
        },
        {
          key: 'assignment',
          title: this.$str('assignment', 'totara_assignment'),
          size: 'sm',
        },
      ],
    };
  },

  computed: {
    hasMultipleAssignments() {
      return this.assignments.length > 1;
    },

    selectedFilter() {
      if (this.assignmentFilter) {
        return {
          assignment_id: this.assignmentFilter,
        };
      }
      return {};
    },
  },

  mounted: function() {},

  methods: {
    showProgressName(data) {
      return (
        data.assignment != null &&
        ![ACTION_TRACKING_START, ACTION_TRACKING_END].includes(
          data.assignment_action
        )
      );
    },

    assignmentFilterUpdated(assignment) {
      this.assignmentFilter = assignment;
    },

    refreshAssignmentList(log) {
      let assignmentList = {};

      log.forEach(entry => {
        if (entry.assignment != null) {
          assignmentList[entry.assignment.id] = {
            id: entry.assignment.id,
            label: entry.assignment.progress_name,
          };
        }
      });

      assignmentList = Object.values(assignmentList);
      if (assignmentList.length >= this.assignments.length) {
        return assignmentList;
      } else {
        return this.assignments;
      }
    },
  },

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
};
</script>

<style lang="scss">
.tui-ActivityLog__ {
  &subtitle {
    padding-top: $totara_style-spacing_2;
    font-weight: bold;
  }
}
</style>

<lang-strings>
  {
    "moodle": [
      "date",
      "description"
    ],
    "totara_competency": [
      "activity_log",
      "activitylog_proficientstatus"
    ],
    "totara_assignment": [
      "assignment"
    ]
  }
</lang-strings>
