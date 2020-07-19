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
        { name: 'xsmall', boundaries: [0, 480] },
        { name: 'small', boundaries: [481, 764] },
        { name: 'medium', boundaries: [765, 1192] },
        { name: 'large', boundaries: [1193, 1396] },
        { name: 'xlarge', boundaries: [1397, 1672] },
      ]"
      @responsive-resize="$_resize"
    >
      <!--
        Wait for the boundary name is populated so that the initial state of several components
        within this layout will be calculated correctly when rendering.
       -->
      <Grid v-if="currentBoundaryName !== null" :direction="gridDirection">
        <GridItem
          :units="gridUnitsLeft"
          :class="{
            'tui-layoutOneColumnWithSidepanel__column--hidden':
              sidePanelIsOpen && onSmallScreen,
          }"
        >
          <h3 class="tui-layoutOneColumnWithSidepanel__heading">
            <slot name="page-title" />
          </h3>
          <slot
            name="column"
            :units="gridUnitsLeft"
            :boundary-name="currentBoundaryName"
            :direction="gridDirection"
          />
        </GridItem>
        <GridItem :units="1" :grows="false" :shrinks="false" />
        <GridItem :units="gridUnitsRight" :grows="false">
          <SidePanel
            ref="sidepanel"
            direction="rtl"
            :animated="!onSmallScreen"
            :sticky="!onSmallScreen"
            :grow-height-on-scroll="!onSmallScreen"
            :show-button-control="true"
            :initially-open="!onSmallScreen"
            :overflows="false"
            @sidepanel-expanding="expandRequest"
            @sidepanel-collapsing="collapseRequest"
          >
            <slot
              name="sidepanel"
              :units="gridUnitsRight"
              :boundary-name="currentBoundaryName"
              :direction="gridDirection"
            />
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
        xsmall: {
          gridDirection: 'horizontal',
          gridUnitsLeftExpanded: 1,
          gridUnitsLeftCollapsed: 9,
          gridUnitsRightExpanded: 10,
          gridUnitsRightCollapsed: 2,
        },
        small: {
          gridDirection: 'horizontal',
          gridUnitsLeftExpanded: 1,
          gridUnitsLeftCollapsed: 9,
          gridUnitsRightExpanded: 10,
          gridUnitsRightCollapsed: 2,
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
        xlarge: {
          gridDirection: 'horizontal',
          gridUnitsLeftExpanded: 6,
          gridUnitsLeftCollapsed: 10,
          gridUnitsRightExpanded: 5,
          gridUnitsRightCollapsed: 1,
        },
      },

      // Note: the initial state of the boundary or side panel should not be set to any default value, as
      // it will calculate the wrong initial state of other components within this layout.
      currentBoundaryName: null,
      sidePanelIsOpen: null,
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
    onSmallScreen() {
      return (
        this.currentBoundaryName === 'xsmall' ||
        this.currentBoundaryName === 'small'
      );
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
