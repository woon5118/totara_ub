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
  @package theme_ventura
-->

<template>
  <div class="tui-layoutOneColumnWithSidepanel">
    <Responsive
      :breakpoints="[
        { name: 'small', boundaries: [0, 764] },
        { name: 'medium', boundaries: [765, 1192] },
        { name: 'large', boundaries: [1193, 1672] },
      ]"
      @responsive-resize="$_resize"
    >
      <Grid :direction="gridDirection">
        <GridItem :units="gridUnitsLeft">
          <h3 class="tui-layoutOneColumnWithSidepanel__heading">
            <slot name="page-title" />
          </h3>
          <slot name="column" :units="gridUnitsLeft" />
        </GridItem>
        <GridItem :units="1" :grows="false" :shrinks="false" />
        <GridItem :units="gridUnitsRight" :grows="false">
          <SidePanel
            ref="sidepanel"
            direction="rtl"
            :animated="currentBoundaryName !== 'small'"
            :sticky="currentBoundaryName !== 'small'"
            :grow-height-on-scroll="currentBoundaryName !== 'small'"
            :show-button-control="currentBoundaryName !== 'small'"
            :initially-open="true"
            :overflows="false"
            @sidepanel-expanding="expandRequest"
            @sidepanel-collapsing="collapseRequest"
          >
            <slot name="sidepanel" :units="gridUnitsRight" />
          </SidePanel>
        </GridItem>
      </Grid>
    </Responsive>
  </div>
</template>

<script>
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Responsive from 'totara_core/components/responsive/Responsive';
import SidePanel from 'totara_core/components/sidepanel/SidePanel';

export default {
  components: {
    Grid,
    GridItem,
    Responsive,
    SidePanel,
  },
  data() {
    return {
      /**
       * Total expanded/collapsed units should equal 11, not 12, as 1 unit is
       * reserved for a GridItem between main content and the SidePanel
       **/
      boundaryDefaults: {
        small: {
          gridDirection: 'vertical',
          gridUnitsLeftExpanded: 12,
          gridUnitsLeftCollapsed: 12,
          gridUnitsRightExpanded: 12,
          gridUnitsRightCollapsed: 12,
        },
        medium: {
          gridDirection: 'horizontal',
          gridUnitsLeftExpanded: 6,
          gridUnitsLeftCollapsed: 10,
          gridUnitsRightExpanded: 5,
          gridUnitsRightCollapsed: 1,
        },
        large: {
          gridDirection: 'horizontal',
          gridUnitsLeftExpanded: 6,
          gridUnitsLeftCollapsed: 10,
          gridUnitsRightExpanded: 5,
          gridUnitsRightCollapsed: 1,
        },
      },
      currentBoundaryName: 'large',
      sidePanelIsOpen: true,
    };
  },
  computed: {
    gridDirection() {
      return this.boundaryDefaults[this.currentBoundaryName].gridDirection;
    },
    gridUnitsLeft() {
      let left = this.sidePanelIsOpen
        ? 'gridUnitsLeftExpanded'
        : 'gridUnitsLeftCollapsed';
      return this.boundaryDefaults[this.currentBoundaryName][left];
    },
    gridUnitsRight() {
      let right = this.sidePanelIsOpen
        ? 'gridUnitsRightExpanded'
        : 'gridUnitsRightCollapsed';
      return this.boundaryDefaults[this.currentBoundaryName][right];
    },
  },
  methods: {
    /**
     * Handles responsive resizing which wraps the grid layout for this page
     *
     * @param {String} boundaryName
     **/
    $_resize(boundaryName) {
      this.currentBoundaryName = boundaryName;
    },

    expandRequest: function() {
      this.sidePanelIsOpen = true;
    },
    collapseRequest: function() {
      this.sidePanelIsOpen = false;
    },
  },
};
</script>
