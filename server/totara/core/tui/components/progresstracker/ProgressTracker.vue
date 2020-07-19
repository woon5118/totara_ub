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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
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
