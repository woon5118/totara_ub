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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module samples
-->

<template>
  <div>
    <div class="tui-sampleLayoutOneColumnWithMultiSidePanel">
      <p>Example: Only center content</p>
      <LayoutOneColumnWithMultiSidePanel
        :show-left-side-panel="false"
        :show-right-side-panel="false"
      >
        <template v-slot:column>
          <p>Center Column Content</p>
        </template>
      </LayoutOneColumnWithMultiSidePanel>
    </div>

    <div class="tui-sampleLayoutOneColumnWithMultiSidePanel">
      <p>Example: Left side panel with content on right</p>
      <LayoutOneColumnWithMultiSidePanel
        :show-left-side-panel="true"
        :show-right-side-panel="false"
        :left-side-panel-animated="false"
        :left-side-panel-initially-open="true"
        :left-side-panel-sticky="false"
        :left-side-panel-limit-height="false"
        :left-side-panel-grow-height-on-scroll="false"
      >
        <template v-slot:sidePanelLeft>
          <p>Left Side Panel</p>
        </template>
        <template v-slot:column>
          <p>Center Column Content</p>
        </template>
      </LayoutOneColumnWithMultiSidePanel>
    </div>

    <div class="tui-sampleLayoutOneColumnWithMultiSidePanel">
      <p>
        Example: Left side panel with center content and side panel on right
      </p>
      <LayoutOneColumnWithMultiSidePanel
        :show-left-side-panel="true"
        :show-right-side-panel="true"
        :left-side-panel-animated="false"
        :right-side-panel-animated="false"
        :left-side-panel-initially-open="true"
        :right-side-panel-initially-open="true"
        :left-side-panel-sticky="false"
        :right-side-panel-sticky="false"
        :left-side-panel-limit-height="false"
        :right-side-panel-limit-height="false"
        :left-side-panel-grow-height-on-scroll="false"
        :right-side-panel-grow-height-on-scroll="false"
      >
        <template v-slot:sidePanelLeft>
          <p>Left Side Panel</p>
        </template>
        <template v-slot:column>
          <p>Center Column Content</p>
        </template>
        <template v-slot:sidePanelRight>
          <p>Right Side Panel</p>
        </template>
      </LayoutOneColumnWithMultiSidePanel>
    </div>

    <div class="tui-sampleLayoutOneColumnWithMultiSidePanel">
      <p>Example: Center content and side panel on right</p>
      <LayoutOneColumnWithMultiSidePanel
        :show-left-side-panel="false"
        :show-right-side-panel="true"
        :right-side-panel-animated="false"
        :right-side-panel-initially-open="true"
        :right-side-panel-sticky="false"
        :right-side-panel-limit-height="false"
        :right-side-panel-grow-height-on-scroll="false"
      >
        <template v-slot:column>
          <p>Center Column Content</p>
        </template>
        <template v-slot:sidePanelRight>
          <p>Right Side Panel</p>
        </template>
      </LayoutOneColumnWithMultiSidePanel>
    </div>

    <div class="tui-sampleLayoutOneColumnWithMultiSidePanel">
      <p>
        Example: Left side panel with center content and side panel on right
        overlaid
      </p>
      <LayoutOneColumnWithMultiSidePanel
        :boundaries="boundariesOverlaid"
        :show-left-side-panel="true"
        :show-right-side-panel="true"
        :left-side-panel-animated="false"
        :right-side-panel-animated="false"
        :left-side-panel-initially-open="true"
        :right-side-panel-initially-open="false"
        :left-side-panel-sticky="false"
        :right-side-panel-sticky="false"
        :left-side-panel-limit-height="false"
        :right-side-panel-limit-height="false"
        :left-side-panel-grow-height-on-scroll="false"
        :right-side-panel-grow-height-on-scroll="false"
      >
        <template v-slot:sidePanelLeft>
          <p>Left Side Panel</p>
        </template>
        <template v-slot:column>
          <p>Center Column Content</p>
        </template>
        <template v-slot:sidePanelRight>
          <p>Right Side Panel</p>
        </template>
      </LayoutOneColumnWithMultiSidePanel>
    </div>

    <div class="tui-sampleLayoutOneColumnWithMultiSidePanel">
      <p>Example: Center content and side panel on right overlaid</p>
      <LayoutOneColumnWithMultiSidePanel
        :boundaries="boundariesOverlaid"
        :show-left-side-panel="false"
        :show-right-side-panel="true"
        :right-side-panel-animated="false"
        :right-side-panel-initially-open="false"
        :right-side-panel-sticky="false"
        :right-side-panel-limit-height="false"
        :right-side-panel-grow-height-on-scroll="false"
      >
        <template v-slot:column>
          <p>
            Center Column Content<br />
            that<br />
            should<br />
            be<br />
            hidden<br />
            when<br />
            SidePanel<br />
            overlays<br />
          </p>
        </template>
        <template v-slot:sidePanelRight>
          <p>Right Side Panel</p>
        </template>
      </LayoutOneColumnWithMultiSidePanel>
    </div>
  </div>
</template>

<script>
import LayoutOneColumnWithMultiSidePanel from 'tui/components/layouts/LayoutOneColumnWithMultiSidePanel';

export default {
  components: {
    LayoutOneColumnWithMultiSidePanel,
  },
  data() {
    return {
      boundariesOverlaid: {
        small: {
          gridDirection: 'vertical',
          gridUnitsOuterLeftExpanded: 12,
          gridUnitsOuterLeftCollapsed: 12,
          gridUnitsOuterRightExpanded: 6,
          gridUnitsOuterRightCollapsed: 11,
          gridUnitsInnerLeftExpanded: 11,
          gridUnitsInnerLeftCollapsed: 11,
          gridUnitsInnerRightExpanded: 12,
          gridUnitsInnerRightCollapsed: 1,
        },
        medium: {
          gridDirection: 'horizontal',
          gridUnitsOuterLeftExpanded: 3,
          gridUnitsOuterLeftCollapsed: 1,
          gridUnitsOuterRightExpanded: 9,
          gridUnitsOuterRightCollapsed: 11,
          gridUnitsInnerLeftExpanded: 11,
          gridUnitsInnerLeftCollapsed: 11,
          gridUnitsInnerRightExpanded: 12,
          gridUnitsInnerRightCollapsed: 1,
        },
        large: {
          gridDirection: 'horizontal',
          gridUnitsOuterLeftExpanded: 2,
          gridUnitsOuterLeftCollapsed: 1,
          gridUnitsOuterRightExpanded: 10,
          gridUnitsOuterRightCollapsed: 11,
          gridUnitsInnerLeftExpanded: 11,
          gridUnitsInnerLeftCollapsed: 11,
          gridUnitsInnerRightExpanded: 12,
          gridUnitsInnerRightCollapsed: 1,
        },
      },
    };
  },
};
</script>

<style lang="scss">
.tui-sampleLayoutOneColumnWithMultiSidePanel {
  margin-bottom: var(--tui-gap-8);

  .tui-layoutOneColumnWithMultiSidePanel {
    border: var(--tui-border-width-thin) solid var(--tui-color-neutral-4);
    .tui-sidePanel__inner {
      width: 100%;
    }

    .tui-layoutOneColumnWithMultiSidePanel__column {
      min-height: 100px;
    }

    .tui-sidePanel {
      &--ltr {
        .tui-sidePanel__inner {
          border-top: none;
          border-bottom: none;
          border-left: none;
        }
      }
      &--rtl {
        .tui-sidePanel__inner {
          border-top: none;
          border-right: none;
          border-bottom: none;
        }
      }
    }
  }
}
</style>
