<template>
  <div class="tui-AssignmentFilter">
    <span class="tui-AssignmentFilter_icon">
      <FlexIcon icon="preferences" size="500" alt="Filters" />
    </span>
    <label
      class="tui-AssignmentFilter_label"
      for="competency-profile-activity-log-filter"
    >
      {{ $str('assignment', 'totara_competency') }}
    </label>
    <select
      id="competency-profile-activity-log-filter"
      v-model="selectedAssignment"
      class="tui-AssignmentFilter_select"
      @change="assignmentUpdated"
    >
      <option value="0">
        {{ $str('all', 'moodle') }}
      </option>
      <option
        v-for="assignment in assignments"
        :key="assignment.id"
        :value="assignment.id"
      >
        {{ assignment.label }}
      </option>
    </select>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';

export default {
  components: {
    FlexIcon,
  },

  props: {
    assignments: {
      required: true,
      type: Array,
    },
    defaultValue: {
      required: false,
      type: Number,
      default: 0,
    },
  },

  data() {
    return {
      selectedAssignment: this.defaultValue,
    };
  },

  watch: {
    defaultFilters(newFilters) {
      this.filters.assignment = newFilters.assignment;
    },
  },

  methods: {
    assignmentUpdated() {
      this.$emit('filter-updated', this.selectedAssignment);
    },
  },
};
</script>
<style lang="scss">
.tui-AssignmentFilter {
  display: block;
  margin-top: var(--tui-gap-4);
  margin-bottom: var(--tui-gap-2);
  padding: var(--tui-gap-2);
  border-top: var(--tui-font-size-1) solid var(--tui-color-neutral-5);
  border-bottom: var(--tui-font-size-1) solid var(--tui-color-neutral-5);

  &_label {
    margin: 0;
    padding-right: var(--tui-gap-4);
    padding-left: var(--tui-gap-2);
    vertical-align: middle;
  }

  &_icon > .flex-icon {
    margin-bottom: 2px;
  }

  &_select {
    display: inline-block;
    min-width: 200px;
  }
}
</style>
<lang-strings>
    {
      "moodle": [
        "all"
      ],
      "totara_competency": [
        "assignment"
      ]
    }
</lang-strings>
