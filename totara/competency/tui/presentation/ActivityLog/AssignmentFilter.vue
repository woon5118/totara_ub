<template>
  <div class="tui-AssignmentFilter">
    <span class="tui-AssignmentFilter_icon">
      <FlexIcon icon="preferences" size="500" alt="Filters" />
    </span>
    <label
      class="tui-AssignmentFilter_label"
      for="competency-profile-activity-log-filter"
    >
      {{ $str('assignment', 'totara_assignment') }}
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
import FlexIcon from 'totara_core/containers/icons/FlexIcon';

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
  margin-top: $totara_style-spacing_4;
  margin-bottom: $totara_style-spacing_2;
  padding: $totara_style-spacing_2;
  border-top: $totara_style-size_1 solid $totara_style-color_neutral_5;
  border-bottom: $totara_style-size_1 solid $totara_style-color_neutral_5;

  &_label {
    margin: 0;
    padding-right: $totara_style-spacing_4;
    padding-left: $totara_style-spacing_2;
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
      "totara_assignment": [
        "assignment"
      ]
    }
</lang-strings>
