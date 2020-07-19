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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_core
-->

<template>
  <div
    ref="progress"
    class="tui-progress"
    :class="{
      'tui-progress--small': small,
      'tui-progress--hideBackground': hideBackground,
    }"
    :aria-valuetext="valueText"
    :aria-valuenow="value"
    :aria-valuemin="min"
    :aria-valuemax="max"
    role="progressbar"
  >
    <div ref="progress-bar" class="tui-progress__bar" :style="progressStyle">
      <span
        ref="progressLabel"
        class="tui-progress__label"
        :class="!hideValue && insideLabel && 'tui-progress__label--inside'"
        >{{ valueText }}</span
      >
    </div>
    <span
      v-show="!hideValue && !insideLabel"
      class="tui-progress__label tui-progress__label--outside"
      >{{ valueText }}</span
    >
  </div>
</template>

<script>
export default {
  props: {
    small: {
      type: Boolean,
      default: false,
    },
    value: {
      type: Number,
      default: 0,
    },
    min: {
      type: Number,
      default: 0,
    },
    max: {
      type: Number,
      default: 100,
    },
    hideValue: {
      type: Boolean,
      default: false,
    },
    format: {
      type: String,
      default: 'percent',
      validator: f => ['number', 'percent'].includes(f),
    },
    completedText: {
      type: [Boolean, String],
      default: false,
    },
    hideBackground: {
      type: Boolean,
      default: false,
    },
    showEmptyState: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      progressStyle: {},
      insideLabel: false,
    };
  },
  computed: {
    fraction() {
      if (this.value > this.max) {
        return 1;
      }
      if (this.value < this.min) {
        return 0;
      }
      return Number((this.value - this.min) / (this.max - this.min));
    },
    valueText() {
      if (this.completedText && this.value === this.max) {
        return typeof this.completedText === 'string'
          ? this.completedText
          : this.$str('completed', 'totara_core');
      }

      if (this.format === 'percent') {
        return `${(this.fraction * 100).toFixed(0)}%`;
      }

      return this.value;
    },
  },
  watch: {
    value: {
      immediate: true,
      handler() {
        this.$_setProgressStyle();

        if (!this.hideValue) {
          this.$nextTick(() => {
            this.$_setLabelStyle();
          });
        }
      },
    },
  },
  methods: {
    $_setLabelStyle() {
      const containerW = this.$refs.progress.offsetWidth;
      const labelW = this.$refs.progressLabel.offsetWidth;

      this.insideLabel = labelW < containerW * this.fraction;
    },
    $_setProgressStyle() {
      let w,
        bgColor = '';

      //handle empty state styling
      if (this.showEmptyState && this.value <= this.min) {
        w = '2%';
        bgColor = '#e6e4e4';
      } else {
        w = `${this.fraction * 100}%`;
      }

      this.progressStyle = {
        width: w,
        backgroundColor: bgColor,
      };
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "completed"
    ]
  }
</lang-strings>
