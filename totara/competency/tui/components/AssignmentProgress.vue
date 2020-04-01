<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @package totara_competency
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
import theme from 'totara_core/theme';

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
