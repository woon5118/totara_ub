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
        <Popover :triggers="['hover', 'focus']">
          <template v-slot:trigger>
            <span>{{ props.row.date }}</span>
          </template>
          <span>{{ props.row.datetime }}</span>
        </Popover>
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

import Popover from 'totara_core/components/popover/Popover';
import List from 'totara_competency/components/List';
import ProficientStatus from './ProficientStatus';
import Description from './Description';
import AssignmentFilter from './AssignmentFilter';

import ActivityLogQuery from '../../../webapi/ajax/activity_log.graphql';

export default {
  components: {
    Popover,
    Description,
    ProficientStatus,
    List,
    AssignmentFilter,
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
          title: this.$str('assignment', 'totara_competency'),
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
            label: this.getFilterLabel(entry),
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

    isDirectlyAssigned(assignment) {
      return (
        assignment.user_group_type === 'user' &&
        (assignment.type === 'admin' || assignment.type === 'other')
      );
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
    padding-top: var(--tui-gap-2);
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
      "assignment",
      "activitylog_proficientstatus",
      "progress_name_by_user"
    ]
  }
</lang-strings>
