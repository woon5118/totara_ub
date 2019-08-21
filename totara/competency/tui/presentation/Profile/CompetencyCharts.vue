<template>
  <div class="tui-CompetencyCharts">
    <div
      v-for="(item, key) in data.items"
      :key="key"
      class="tui-CompetencyCharts__chart"
    >
      <IndividualAssignmentProgress :assignment-progress="item" />
    </div>
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
  justify-content: center;

  &__chart {
    width: calc(100% - 0.5rem);
    padding: 0.5rem;
    margin: 0 0.5rem 1rem 0;
    border: 1px #999c9c solid;
    border-radius: 1rem;

    @media (min-width: $totara_style-screen_sm_min) and (max-width: $totara_style-screen_xl_min) {
      width: $totara_style-screen_sm_min - 0.5rem;
    }

    @media (min-width: $totara_style-screen_xl_min) {
      width: calc(50% - 0.5rem);
    }
  }
}
</style>
<lang-strings>
  {}
</lang-strings>
