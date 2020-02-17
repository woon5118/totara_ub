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
  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_competency
-->

<template>
  <div class="tui-competencyProfileCurrentProgress">
    <Grid :stack-at="900">
      <GridItem :units="10">
        <ul class="tui-competencyProfileCurrentProgress__progress">
          <li v-for="(item, key) in data" :key="key">
            <AssignmentProgress :progress="item" />
          </li>
        </ul>
      </GridItem>
      <GridItem :units="2">
        <div class="tui-competencyProfileCurrentProgress__userDetails">
          <div
            v-if="latestAchievement"
            class="tui-competencyProfileCurrentProgress__latestAchievement"
          >
            <div
              class="tui-competencyProfileCurrentProgress__latestAchievement-header"
            >
              {{ $str('latest_achievement', 'totara_competency') }}
            </div>
            <div
              class="tui-competencyProfileCurrentProgress__latestAchievement-content"
            >
              {{ latestAchievement }}
            </div>
          </div>
        </div>
      </GridItem>
    </Grid>
  </div>
</template>

<script>
import AssignmentProgress from 'totara_competency/components/AssignmentProgress';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';

export default {
  components: {
    AssignmentProgress,
    Grid,
    GridItem,
  },

  props: {
    data: {
      required: true,
      type: Array,
    },
    latestAchievement: {
      required: true,
      validator: prop => typeof prop === 'string' || prop === null, // String or null
    },
  },
};
</script>

<lang-strings>
{
  "pathway_manual": [
    "rate_competencies"
  ],
  "totara_competency": [
    "assign_competencies",
    "self_assign_competencies",
    "latest_achievement"
  ]
}
</lang-strings>
