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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-progressTracker">
    <OverflowDetector v-slot="{ measuring }" @change="overflowChanged">
      <ol
        class="tui-progressTrackerItems"
        :class="{
          'tui-progressTrackerItems--vertical': !measuring && vertical,
        }"
      >
        <ProgressTrackerItem
          v-for="(item, index) in itemsWithState"
          :key="index"
          :gap="gap"
          :label="item.label"
          :state="item.state"
          :target="item.target"
          :current="item.current"
          :popover-trigger-type="popoverTriggerType"
          :achieved-goal-string="achievedGoalString"
          :target-string="targetString"
        >
          <template v-slot:popover-content>
            <template v-if="!$scopedSlots['custom-popover-content']">
              <div v-html="item.description" />
            </template>
            <slot
              name="custom-popover-content"
              :description="item.description"
              :label="item.label"
              :target="item.target"
            />
          </template>
        </ProgressTrackerItem>
      </ol>
    </OverflowDetector>
  </div>
</template>

<script>
import OverflowDetector from 'totara_core/components/util/OverflowDetector';
import ProgressTrackerItem from 'totara_core/components/progresstracker/ProgressTrackerItem';

export default {
  components: {
    OverflowDetector,
    ProgressTrackerItem,
  },

  props: {
    gap: String,
    currentId: Number,
    targetId: Number,
    items: {
      type: Array,
      required: true,
    },
    popoverTriggerType: Array,
    achievedGoalString: String,
    targetString: String,
  },

  data() {
    return {
      vertical: false,
    };
  },

  computed: {
    /**
     * Create item data structure with correct state based on provided currentID & targetID
     *
     * @return {Array}
     */
    itemsWithState() {
      let stateArray;
      let stateString = 'pending';
      let target = false;
      let currentIndex;
      let targetIndex;

      // Check if minimum target item exists
      if (this.targetId) {
        targetIndex = this.items.findIndex(({ id }) => id === this.targetId);
      }

      // Check if current item exists
      if (this.currentId) {
        currentIndex = this.items.findIndex(({ id }) => id === this.currentId);
        if (currentIndex > -1) {
          stateString = 'complete';
        }
      }

      // Check if target has been met
      if (
        targetIndex > -1 &&
        currentIndex > -1 &&
        currentIndex >= targetIndex
      ) {
        stateString = 'achieved';
      }

      stateArray = this.items.map(function(elem, index) {
        if (currentIndex > -1 && index > currentIndex) {
          stateString = 'pending';
        }

        if (targetIndex > -1 && index >= targetIndex) {
          target = true;
        }

        return {
          current: currentIndex === index,
          description: elem.description,
          id: elem.id,
          label: elem.label,
          state: stateString,
          target: target,
        };
      });
      return stateArray;
    },
  },

  methods: {
    /**
     * Switch vertical Bool to true when content is overflowing
     */
    overflowChanged({ overflowing }) {
      this.vertical = overflowing;
    },
  },
};
</script>
