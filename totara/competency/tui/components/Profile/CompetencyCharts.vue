<template>
  <div class="tui-CompetencyCharts">
    <div
      v-for="(item, key) in data.items"
      :key="key + item.name + item.overall_progress"
      class="tui-CompetencyCharts__chart"
    >
      <IndividualAssignmentProgress
        :assignment-progress="item"
        :user-id="userId"
        :is-current-user="isCurrentUser"
      />
    </div>
  </div>
</template>

<script>
import IndividualAssignmentProgress from 'totara_competency/components/IndividualAssignmentProgress';

export default {
  components: {
    IndividualAssignmentProgress,
  },

  props: {
    data: {
      required: true,
      type: Object,
    },
    userId: {
      type: Number,
      required: true,
    },
    isCurrentUser: {
      type: Boolean,
      required: true,
    },
  },
};
</script>
<style lang="scss">
.tui-CompetencyCharts {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;

  &__chart {
    width: calc(100% - 0.5rem);
    margin: 0 0.5rem 1rem 0;
    padding: 3rem 2rem;
    border: 1px #e7e7e7 solid;
    border-radius: 0.3rem;

    @media (min-width: $tui-screen-sm) and (max-width: $tui-screen-xl) {
      width: $tui-screen-sm - 0.5rem;
    }

    @media (min-width: $tui-screen-xl) {
      width: calc(50% - 0.5rem);
    }
  }
}
</style>
