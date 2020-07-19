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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-responsive-example">
    <Responsive
      v-slot="slotProps"
      :breakpoints="[
        { name: 'small', boundaries: [0, 520] },
        { name: 'medium', boundaries: [521, 768] },
        { name: 'large', boundaries: [767, 1600] },
      ]"
      @responsive-resize="resize"
    >
      <div v-if="slotProps.currentBoundaryName === 'small'">
        <p>Rendering for the <code>small</code> boundaryName</p>
      </div>
      <div v-if="slotProps.currentBoundaryName === 'medium'">
        <p>Rendering for the <code>medium</code> boundaryName</p>
      </div>
      <div v-if="slotProps.currentBoundaryName === 'large'">
        <p>Rendering for the <code>large</code> boundaryName</p>
      </div>

      <Grid :direction="gridProps.gridDirection">
        <GridItem
          :units="gridProps.gridItems[0].units"
          :order="gridProps.gridItems[0].order"
          >GridItem 1</GridItem
        >
        <GridItem
          :units="gridProps.gridItems[1].units"
          :order="gridProps.gridItems[1].order"
          >GridItem 2</GridItem
        >
      </Grid>
    </Responsive>
  </div>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Responsive from 'totara_core/components/responsive/Responsive';

export default {
  components: {
    Grid,
    GridItem,
    Responsive,
  },
  data() {
    return {
      gridProps: {
        gridDirection: 'horizontal',
        gridItems: [{ units: 3 }, { units: 9 }],
      },
    };
  },
  methods: {
    /**
     * Handles responsive resizing which wraps the grid layout for this page
     **/
    resize(boundaryName) {
      switch (boundaryName) {
        case 'small':
          this.gridProps = {
            gridDirection: 'vertical',
            gridItems: [
              { units: 10, order: 2 },
              { units: 2, order: 1 },
            ],
          };
          break;
        case 'medium':
          this.gridProps = {
            gridDirection: 'horizontal',
            gridItems: [
              { units: 6, order: 1 },
              { units: 6, order: 2 },
            ],
          };
          break;
        case 'large':
          this.gridProps = {
            gridDirection: 'horizontal',
            gridItems: [
              { units: 2, order: 1 },
              { units: 10, order: 2 },
            ],
          };
          break;
        default:
          break;
      }
    },
  },
};
</script>
<style lang="scss">
.tui-responsive-example .tui-grid {
  background-color: rgba(255, 0, 0, 0.2);
  * {
    background-color: rgba(0, 255, 0, 0.2);
  }
  * * {
    background-color: rgba(0, 0, 255, 0.2);
  }
}
</style>
