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
  <li
    class="tui-progressTrackerItem"
    :class="[
      'tui-progressTrackerItem--' + gap,
      'tui-progressTrackerItem--' + state,
    ]"
    :aria-current="current"
  >
    <div class="tui-progressTrackerItem__content">
      <p v-if="target" class="tui-progressTrackerItem__target">
        {{ targetItemString }}
      </p>

      <PopoverTrigger
        :triggers="popoverTriggerType"
        :ui-element="$refs.popover"
        @open-changed="setOpen"
      >
        <ProgressTrackerCircle ref="trigger" :state="state" :target="target" />
      </PopoverTrigger>

      <PopoverTrigger
        :triggers="popoverTriggerType"
        :ui-element="$refs.popover"
        @open-changed="setOpen"
      >
        <div
          class="tui-progressTrackerItem__label"
          :class="{ 'tui-progressTrackerItem__label-current': current }"
        >
          <Button :text="label" :styleclass="{ transparent: true }" />
        </div>
      </PopoverTrigger>

      <p class="tui-progressTrackerItem__status">
        {{ $str('status', 'moodle') + ': ' + stateString }}
      </p>
    </div>
    <Popover
      ref="popover"
      :triggers="popoverTriggerType"
      :open="open"
      :reference="$refs.trigger"
      position="top"
      @request-close="setOpen(false)"
    >
      <slot name="popover-content" />
    </Popover>
  </li>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Popover from 'totara_core/components/popover/Popover';
import ProgressTrackerCircle from 'totara_core/components/progresstracker/ProgressTrackerCircle';
import PopoverTrigger from 'totara_core/components/popover/PopoverTrigger';

export default {
  components: {
    Button,
    Popover,
    ProgressTrackerCircle,
    PopoverTrigger,
  },

  props: {
    achievedGoalString: {
      type: String,
    },
    popoverTriggerType: {
      default: () => ['click'],
      type: Array,
    },
    current: {
      type: Boolean,
      default: false,
    },
    label: {
      type: String,
    },
    gap: {
      default: 'medium',
      type: String,
      validator: function(value) {
        const allowedOptions = ['small', 'medium', 'large'];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    state: {
      default: 'pending',
      type: String,
      validator: function(value) {
        const allowedOptions = ['pending', 'complete', 'achieved'];
        return allowedOptions.indexOf(value) !== -1;
      },
    },
    target: {
      default: false,
      type: Boolean,
    },
    targetString: {
      type: String,
    },
  },

  data() {
    return {
      open: false,
    };
  },

  computed: {
    /**
     * Accessibility string for state
     *
     * @return {Array}
     */
    stateString() {
      if (this.state === 'pending') {
        return this.$str('completion-n', 'completion');
      } else if (this.state === 'complete') {
        return this.$str('completion-y', 'completion');
      } else {
        return (
          this.achievedGoalString ||
          this.$str('a11yachievedrequiredgoal', 'totara_core')
        );
      }
    },
    /**
     * Accessibility string for target
     *
     * @return {String}
     */
    targetItemString() {
      return (
        this.targetString || this.$str('a11yachievementtarget', 'totara_core')
      );
    },
  },

  methods: {
    setOpen(value) {
      this.open = value;
    },
  },
};
</script>

<lang-strings>
{
  "completion": ["completion-n", "completion-y"],
  "moodle": ["status"],
  "totara_core": ["a11yachievedrequiredgoal", "a11yachievementtarget"]
}
</lang-strings>
