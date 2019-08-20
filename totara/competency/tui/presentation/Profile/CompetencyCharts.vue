<template>
  <div>
    <template v-if="data.items && data.items.length === 1">
      <div class="tui-CompetencyCharts__single_chart_container">
        <IndividualAssignmentProgress
          :assignment-progress="data.items[0]"
          class="tui-CompetencyCharts__single_chart"
        />
      </div>
    </template>
    <template v-else>
      <div class="tui-CompetencyCharts">
        <div
          v-for="(item, key) in data.items"
          :key="key"
          :name="item.name"
          :item-key="key"
          :is-open="data.items.length <= 5"
          class="tui-CompetencyCharts__chart"
        >
          <IndividualAssignmentProgress :assignment-progress="item" />
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import IndividualAssignmentProgress from '../../container/IndividualAssignmentProgress';

export default {
  components: {
    IndividualAssignmentProgress,
  },

  props: {
    data: {
      required: true,
      type: Object,
    },
  },

  data: function() {
    return {};
  },

  computed: {},

  watch: {},

  mounted: function() {},

  methods: {},
};
</script>
<style lang="scss">
.tui-CompetencyCharts {
  display: flex;
  flex-wrap: wrap;

  &__single_chart_container {
    display: flex;
    justify-content: center;
  }

  &__single_chart {
    max-width: $totara_style-screen_sm_min;
  }

  &__chart {
    width: calc(100% - 0.5rem);
    padding: 0.5rem;
    margin: 0 0.5rem 1rem 0;
    border: 1px #999c9c solid;
    border-radius: 1rem;

    @media (min-width: $totara_style-screen_sm_min) {
      width: calc(50% - 0.5rem);
    }
  }
}
</style>
<lang-strings>
  {}
</lang-strings>
