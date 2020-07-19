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
  @package totara_samples
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
import LayoutOneColumnWithMultiSidePanel from 'totara_core/components/layouts/LayoutOneColumnWithMultiSidePanel';

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
