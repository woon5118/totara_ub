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
  <Responsive
    v-slot="{ currentBoundaryName }"
    :breakpoints="[
      { name: 'small', boundaries: [0, 1330] },
      { name: null, boundaries: [1331, 1331] },
    ]"
  >
    <Grid v-if="data.items && data.items.length > 0" :stack-at="1329">
      <GridItem
        v-for="(item, key) in data.items"
        :key="key + item.name + item.overall_progress"
        :units="6"
      >
        <div
          class="tui-competencyCharts__chart"
          :class="{
            'tui-competencyCharts__chart--center':
              currentBoundaryName == 'small',
          }"
        >
          <IndividualAssignmentProgress
            :assignment-progress="item"
            :user-id="userId"
            :is-current-user="isCurrentUser"
          />
        </div>
      </GridItem>
    </Grid>
  </Responsive>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Responsive from 'totara_core/components/responsive/Responsive';
import IndividualAssignmentProgress from 'totara_competency/components/IndividualAssignmentProgress';

export default {
  components: {
    Grid,
    GridItem,
    Responsive,
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
