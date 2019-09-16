<template>
  <PercentageDoughnut
    :header="progress.name"
    :percentage="progress.overall_progress"
    :label="$str('proficient', 'totara_competency')"
    :label-font-size="14"
    :percentage-font-size="30"
    :square="true"
    label-color="#000"
    color="#DA7E31"
    background-color="#ECECEC"
    class="tui-assignmentProgress__progress-card"
  />
</template>

<script>
import PercentageDoughnut from 'totara_core/presentation/chartjs/PercentageDoughnut';

export default {
  components: { PercentageDoughnut },
  props: {
    progress: {
      required: true,
      type: Object,
    },
  },
};
</script>
<style lang="scss">
.tui-assignmentProgress__ {
  &progress-card {
    width: 175px;
    padding: 1rem;

    .tui-chartJs__header {
      font-size: $totara_style-size_16;
      padding-bottom: 2rem;
    }
  }
}
</style>
<lang-strings>
    {
        "totara_competency" : [
            "proficient"
        ]
    }
</lang-strings>
