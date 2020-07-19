<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @package theme_ventura
-->

<!--
This layout is capable of the following:
  1. Layout where no side panels are needed.
  2. Left side panel with one column.
  3. Right side panel with one column.
  4. Left and right side panel with one column in middle.

  For a visual example please refer to:
   totara/samples/tui/components/samples/totara_core/layouts/LayoutOneColumnWithMultiSidePanel.vue
-->
<template>
  <div class="tui-layoutOneColumnWithMultiSidePanel">
    <Responsive
      :breakpoints="responsiveBreakpoints"
      @responsive-resize="$_resize"
    >
      <Grid
        :direction="gridDirection"
        :stack-at="stackAt"
        :use-vertical-gap="false"
      >
        <!-- LeftSide -->
        <GridItem
          v-if="showLeftSidePanel"
          :units="gridUnitsOuterLeft"
          :grows="true"
        >
          <!-- LeftSidePanel -->
          <SidePanel
            ref="sidePanelLeft"
            class="tui-layoutOneColumnWithMultiSidePanel__leftSidePanel"
            direction="ltr"
            :animated="leftAnimated"
            :sticky="leftSticky"
            :limit-height="leftSidePanelLimitHeight"
            :grow-height-on-scroll="leftGrowHeightOnScroll"
            :initially-open="leftSidePanelInitiallyOpen"
            :overflows="leftSidePanelOverflows"
            :show-button-control="showLeftSidePanelControl"
            @sidepanel-expanding="expandLeftRequest"
            @sidepanel-collapsing="collapseLeftRequest"
          >
            <slot
              name="sidePanelLeft"
              :grid-direction="gridDirection"
              :units="gridUnitsOuterLeft"
            />
          </SidePanel>
          <!-- /LeftSidePanel -->
        </GridItem>
        <!-- /LeftSide -->

        <!-- RightSide -->
        <GridItem
          class="tui-layoutOneColumnWithMultiSidePanel__outerRight"
          :units="gridUnitsOuterRight"
          :grows="true"
        >
          <Grid direction="horizontal">
            <!-- Column -->
            <GridItem
              class="tui-layoutOneColumnWithMultiSidePanel__column"
              :units="gridUnitsInnerLeft"
              :grows="true"
            >
              <div
                class="tui-layoutOneColumnWithMultiSidePanel__columnContainer"
                :style="styleInnerLeft"
              >
                <slot
                  name="column"
                  :grid-direction="gridDirection"
                  :units="gridUnitsInnerLeft"
                />
              </div>
            </GridItem>
            <!-- /Column -->

            <!-- RightSidePanel -->
            <GridItem
              v-if="showRightSidePanel"
              :units="unitsInnerRight"
              :grows="true"
            >
              <div
                ref="sidePanelRightContainer"
                class="tui-layoutOneColumnWithMultiSidePanel__rightSidePanelContainer"
                :style="styleInnerRight"
              >
                <SidePanel
                  ref="sidePanelRight"
                  class="tui-layoutOneColumnWithMultiSidePanel__rightSidePanel"
                  direction="rtl"
                  :animated="rightAnimated"
                  :sticky="rightSticky"
                  :limit-height="rightSidePanelLimitHeight"
                  :grow-height-on-scroll="rightGrowHeightOnScroll"
                  :initially-open="rightSidePanelInitiallyOpen"
                  :overflows="rightSidePanelOverflows"
                  :show-button-control="showRightSidePanelControl"
                  @sidepanel-expanding="expandRightRequest"
                  @sidepanel-collapsing="collapseRightRequest"
                >
                  <slot
                    name="sidePanelRight"
                    :grid-direction="gridDirection"
                    :units="unitsInnerRight"
                  />
                </SidePanel>
              </div>
            </GridItem>
            <!-- /RightSidePanel -->
          </Grid>
        </GridItem>
        <!-- /RightSide -->
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
  props: {
    /**
     * See Responsive implementation for more details on breakpoints.
     **/
    breakpoints: {
      type: Array,
    },

    /**
     * Object defining properties for each breakpoint. See this.boundaryDefaults
     * for an example on usage.
     **/
    boundaries: {
      type: Object,
    },

    /**
     * Name of key identifying a property in boundaries, used as the default boundary.
     */
    defaultBoundary: {
      type: String,
      default: 'large',
    },

    /**
     * See Grid implementation for more details on stacking.
     */
    stackAt: {
      type: Number,
      default: 764,
    },

    /**
     * Whether the SidePanels should display or not.
     */
    showLeftSidePanel: {
      type: Boolean,
      default: true,
    },
    showRightSidePanel: {
      type: Boolean,
      default: true,
    },

    /**
     * Whether the SidePanel control (open and close button) should be displayed or not.
     */
    showLeftSidePanelControl: {
      type: Boolean,
      default: true,
    },
    showRightSidePanelControl: {
      type: Boolean,
      default: true,
    },

    /**
     * Whether the SidePanel inner container should invoke a scrollbar if its
     * contents exceed its available height
     **/
    leftSidePanelOverflows: {
      type: Boolean,
      default: false,
    },
    rightSidePanelOverflows: {
      type: Boolean,
      default: false,
    },

    /**
     * Whether the SidePanel should be open when it is first rendered
     **/
    leftSidePanelInitiallyOpen: {
      type: Boolean,
      default: false,
    },
    rightSidePanelInitiallyOpen: {
      type: Boolean,
      default: false,
    },

    /**
     * Whether the SidePanel should remain wholly in the viewport when a long
     * page is scrolled
     **/
    leftSidePanelSticky: {
      type: Boolean,
      default: null,
    },
    rightSidePanelSticky: {
      type: Boolean,
      default: null,
    },

    /**
     * Whether transition lifecycles should be managed for CSS-based animations
     **/
    leftSidePanelAnimated: {
      type: Boolean,
      default: null,
    },
    rightSidePanelAnimated: {
      type: Boolean,
      default: null,
    },

    /**
     * Whether to set a CSS max-height value that is not `initial`
     **/
    leftSidePanelLimitHeight: {
      type: Boolean,
      default: null,
    },
    rightSidePanelLimitHeight: {
      type: Boolean,
      default: null,
    },

    /**
     * Whether the SidePanel's height should grow when scrolling, up to a max
     * height of the current size of the viewport
     **/
    leftSidePanelGrowHeightOnScroll: {
      type: Boolean,
      default: null,
    },
    rightSidePanelGrowHeightOnScroll: {
      type: Boolean,
      default: null,
    },
  },
  data() {
    return {
      breakpointDefaults: [
        { name: 'small', boundaries: [0, 764] },
        { name: 'medium', boundaries: [765, 1192] },
        { name: 'large', boundaries: [1193, 1672] },
      ],
      boundaryDefaults: {
        small: {
          gridDirection: 'vertical',
          gridUnitsOuterLeftExpanded: 12,
          gridUnitsOuterLeftCollapsed: 12,
          gridUnitsOuterRightExpanded: 6,
          gridUnitsOuterRightCollapsed: 11,
          gridUnitsInnerLeftExpanded: 6,
          gridUnitsInnerLeftCollapsed: 11,
          gridUnitsInnerRightExpanded: 6,
          gridUnitsInnerRightCollapsed: 1,
        },
        medium: {
          gridDirection: 'horizontal',
          gridUnitsOuterLeftExpanded: 3,
          gridUnitsOuterLeftCollapsed: 1,
          gridUnitsOuterRightExpanded: 9,
          gridUnitsOuterRightCollapsed: 11,
          gridUnitsInnerLeftExpanded: 6,
          gridUnitsInnerLeftCollapsed: 11,
          gridUnitsInnerRightExpanded: 6,
          gridUnitsInnerRightCollapsed: 1,
        },
        large: {
          gridDirection: 'horizontal',
          gridUnitsOuterLeftExpanded: 2,
          gridUnitsOuterLeftCollapsed: 1,
          gridUnitsOuterRightExpanded: 10,
          gridUnitsOuterRightCollapsed: 11,
          gridUnitsInnerLeftExpanded: 6,
          gridUnitsInnerLeftCollapsed: 11,
          gridUnitsInnerRightExpanded: 6,
          gridUnitsInnerRightCollapsed: 1,
        },
      },
      currentBoundaryName: this.defaultBoundary,
      leftSidePanelIsOpen: this.leftSidePanelInitiallyOpen,
      rightSidePanelIsOpen: this.rightSidePanelInitiallyOpen,
      styleInnerLeft: {},
      styleInnerRight: {},
      unitsInnerRight: 1,
    };
  },
  computed: {
    responsiveBreakpoints() {
      return this.breakpoints || this.breakpointDefaults;
    },

    responsiveBoundaries() {
      return this.boundaries || this.boundaryDefaults;
    },

    gridDirection() {
      return this.responsiveBoundaries[this.currentBoundaryName].gridDirection;
    },

    gridUnitsOuterLeft() {
      // If left SidePanel should not be shown then it takes up no columns.
      if (!this.showLeftSidePanel) return 0;

      let left = this.leftSidePanelIsOpen
        ? 'gridUnitsOuterLeftExpanded'
        : 'gridUnitsOuterLeftCollapsed';

      return this.responsiveBoundaries[this.currentBoundaryName][left];
    },

    gridUnitsOuterRight() {
      if (this.gridUnitsOuterLeft === 0) return 12;

      let right = this.leftSidePanelIsOpen
        ? 'gridUnitsOuterRightExpanded'
        : 'gridUnitsOuterRightCollapsed';

      return this.responsiveBoundaries[this.currentBoundaryName][right];
    },

    gridUnitsInnerLeft() {
      if (this.gridUnitsInnerRight === 0) return 12;

      let left = this.rightSidePanelIsOpen
        ? 'gridUnitsInnerLeftExpanded'
        : 'gridUnitsInnerLeftCollapsed';

      return this.responsiveBoundaries[this.currentBoundaryName][left];
    },

    gridUnitsInnerRight() {
      // If right SidePanel should not be shown then it takes up no columns.
      if (!this.showRightSidePanel) return 0;

      let right = this.rightSidePanelIsOpen
        ? 'gridUnitsInnerRightExpanded'
        : 'gridUnitsInnerRightCollapsed';

      return this.responsiveBoundaries[this.currentBoundaryName][right];
    },

    leftSticky() {
      if (this.leftSidePanelSticky != null) {
        return this.leftSidePanelSticky;
      }
      return this.currentBoundaryName !== 'small';
    },

    rightSticky() {
      if (this.leftSidePanelSticky != null) {
        return this.rightSidePanelSticky;
      }
      return this.currentBoundaryName !== 'small';
    },

    leftAnimated() {
      if (this.leftSidePanelAnimated != null) {
        return this.leftSidePanelAnimated;
      }
      return this.currentBoundaryName !== 'small';
    },

    rightAnimated() {
      if (this.rightSidePanelAnimated != null) {
        return this.rightSidePanelAnimated;
      }
      return this.currentBoundaryName !== 'small';
    },

    leftGrowHeightOnScroll() {
      if (this.leftSidePanelGrowHeightOnScroll != null) {
        return this.leftSidePanelGrowHeightOnScroll;
      }
      return this.currentBoundaryName !== 'small';
    },

    rightGrowHeightOnScroll() {
      if (this.rightSidePanelGrowHeightOnScroll != null) {
        return this.rightSidePanelGrowHeightOnScroll;
      }
      return this.currentBoundaryName !== 'small';
    },
  },

  watch: {
    gridUnitsInnerRight: {
      immediate: true,
      handler(units) {
        // If units is 12 then we need to do something special as the inner left and inner
        // right needs to display on the same line but with the inner right overlaying the
        // inner left.
        if (units === 12) {
          this.unitsInnerRight = this.responsiveBoundaries[
            this.currentBoundaryName
          ].gridUnitsInnerRightCollapsed;
          this.styleInnerRight.position = 'absolute';
          this.styleInnerRight.top = 0;
          this.styleInnerRight.left = 0;
          this.styleInnerRight.right = 0;
          this.styleInnerLeft.opacity = 0;
        } else {
          this.styleInnerRight = {};
          this.unitsInnerRight = units;
          this.styleInnerLeft.opacity = 1;
        }
      },
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
      this.$emit('responsive-resize', boundaryName);
    },

    expandLeftRequest() {
      this.leftSidePanelIsOpen = true;
    },

    collapseLeftRequest() {
      this.leftSidePanelIsOpen = false;
    },

    expandRightRequest() {
      this.rightSidePanelIsOpen = true;
    },

    collapseRightRequest() {
      this.rightSidePanelIsOpen = false;
    },
  },
};
</script>
