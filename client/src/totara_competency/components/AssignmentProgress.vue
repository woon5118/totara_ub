<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @module totara_competency
-->

<template>
  <div class="tui-assignmentProgress">
    <PercentageDoughnut
      :percentage="progress.overall_progress"
      :label="$str('chart_label_proficient', 'totara_competency')"
      :label-font-size="13"
      label-font-weight="bold"
      :percentage-font-size="24"
      :square="true"
      :label-color="labelColor"
      :color="color"
      :background-color="backgroundColor"
      class="tui-assignmentProgress__progress-card"
    />
    <div class="tui-assignmentProgress__header">
      {{ progress.name }}
    </div>
  </div>
</template>

<script>
import PercentageDoughnut from 'totara_core/components/chartjs/PercentageDoughnut';
import theme from 'tui/theme';

export default {
  components: {
    PercentageDoughnut,
  },

  props: {
    progress: {
      required: true,
      type: Object,
    },
  },

  computed: {
    color: () => theme.getVar('--tui-color-secondary'),
    backgroundColor: () => theme.getVar('--tui-color-neutral-4'),
    labelColor: () => theme.getVar('--tui-color-text'),
  },
};
</script>

<lang-strings>
{
  "totara_competency" : [
    "chart_label_proficient"
  ]
}
</lang-strings>
