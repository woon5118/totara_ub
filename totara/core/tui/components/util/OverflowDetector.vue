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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<script>
import { throttle } from 'totara_core/util';
import { getOffsetRect } from 'totara_core/dom/position';
import ResizeObserver from 'totara_core/polyfills/ResizeObserver';
import { isRtl } from 'totara_core/i18n';

const THROTTLE_UPDATE = 150;

export default {
  data() {
    return {
      measuring: false,
      measured: false,
      overflowing: false,
      visible: Infinity,
    };
  },

  created() {
    // for detecting infinite loops
    this.iters = 0;
    this.failed = false;
  },

  mounted() {
    this.resizeObserver = new ResizeObserver(
      throttle(this.$_update, THROTTLE_UPDATE)
    );
    this.resizeObserver.observe(this.$el);
    this.$_update();
  },

  destroyed() {
    this.resizeObserver.disconnect();
  },

  updated() {
    this.$_update();
  },

  methods: {
    $_update() {
      if (!this.$el || this.failed) {
        return;
      }

      // infinite loop failsafe
      if (this.iters == 0) {
        setTimeout(() => (this.iters = 0), 10);
      }
      this.iters++;
      if (this.iters > 100) {
        console.error(
          '[OverflowDetector] Too many updates. Possible infinite loop, disabling.'
        );
        this.failed = true;
        this.measuring = true;
        return;
      }

      if (this.measuring) {
        // stage 2: measure
        this.$_measure();
        this.measuring = false;
        // mark as measured so we don't get in to an infinite loop with updated()
        this.measured = true;
      } else if (this.measured) {
        // stage 3: unset measured
        this.measured = false;
      } else {
        // stage 1: mark as measuring so we get all items shown
        this.measuring = true;
      }
    },

    /**
     * Check which child elements are overflowing and emit event if that has changed.
     */
    $_measure() {
      const el = this.$el;
      const pos = getOffsetRect(el);

      let overflowing = false;
      let visible = el.children.length;

      // figure out how many children are visible
      for (let i = 0; i < el.children.length; i++) {
        const child = el.children[i];
        const childPos = getOffsetRect(child);
        const overflowRTL = isRtl() ? childPos.left < pos.left - 1 : false;
        const overflowLTR = !isRtl() ? childPos.right > pos.right + 1 : false;
        if (
          overflowLTR ||
          overflowRTL ||
          childPos.bottom - pos.top - 1 > pos.height
        ) {
          // we hit our first non-visible child
          // i starts at 0, so it is equal to the number of visible children
          visible = i;
          overflowing = true;
          break;
        }
      }

      // emit event if state has changed
      if (this.overflowing != overflowing || this.visible != visible) {
        this.overflowing = overflowing;
        this.visible = visible;
        this.$emit('change', { overflowing, visible });
      }
    },
  },

  render() {
    return this.$scopedSlots.default({
      measuring: this.measuring,
    });
  },
};
</script>
