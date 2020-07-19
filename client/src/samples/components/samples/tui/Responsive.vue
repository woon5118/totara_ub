<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module tui
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
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Responsive from 'tui/components/responsive/Responsive';

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
