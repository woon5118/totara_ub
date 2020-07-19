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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <transition
    :name="transition && 'tui-popoverPositioner--transition-' + transition"
  >
    <div
      v-show="shouldBeOpen"
      class="tui-popoverPositioner"
      :class="[transition && 'tui-popoverPositioner--transition-' + transition]"
      :style="style"
    >
      <slot
        :side="computedSide || position"
        :arrow-distance="arrowDistance"
        :is-open="shouldBeOpen"
      />
    </div>
  </transition>
</template>

<script>
import Vue from 'vue';
import ResizeObserver from 'tui/polyfills/ResizeObserver';
import { throttle } from 'tui/util';
import {
  getDocumentPosition,
  getOffsetRect,
  getViewportRect,
} from '../../js/dom/position';
import { position } from 'tui/lib/popover';
import { Point, Size } from 'tui/geometry';

export default {
  props: {
    position: String,
    open: Boolean,
    /* eslint-disable-next-line vue/require-prop-types */
    referenceElement: {},
    transition: {
      type: String,
      default: 'default',
    },
  },

  data() {
    return {
      location: new Point(0, 0),
      size: new Size(0, 0),
      innerPadding: 0,
      shouldBeOpen: false,
      computedSide: null,
      arrowDistance: 0,
    };
  },

  computed: {
    style() {
      const left = Math.round(this.location.x);
      const top = Math.round(this.location.y);
      return {
        // use translate rather than top/left to avoid popover getting
        // compressed if it's near the right edge of the viewport
        transform: `translate3d(${left}px, ${top}px, 0)`,
      };
    },
  },

  watch: {
    open(open) {
      if (open) {
        Vue.nextTick(() => {
          this.shouldBeOpen = true;
          this.handleResize();
        });
      } else {
        this.shouldBeOpen = false;
      }
    },

    referenceElement() {
      if (this.shouldBeOpen) {
        this.handleResize();
      }
    },
  },

  mounted() {
    this.handleResizeThrottled = throttle(this.handleResize, 150);

    this.resizeObserver = new ResizeObserver(this.handleResizeThrottled);
    this.resizeObserver.observe(this.$el);

    window.addEventListener('resize', this.handleResizeThrottled);
    window.addEventListener('scroll', this.handleResizeThrottled, {
      passive: true,
    });
  },

  destroyed() {
    window.removeEventListener('resize', this.handleResizeThrottled);
    window.removeEventListener('scroll', this.handleResizeThrottled);
    this.resizeObserver.disconnect();
  },

  methods: {
    updatePosition() {
      if (!this.referenceElement) return;

      const refRect = getOffsetRect(this.referenceElement);
      const offsetParentPosition = getDocumentPosition(
        this.referenceElement.offsetParent
      );
      const viewport = getViewportRect(offsetParentPosition).sub(
        offsetParentPosition
      );

      const pos = position({
        position: this.position.split('-'),
        ref: refRect,
        viewport,
        size: this.size,
        padding: this.innerPadding,
      });

      this.computedSide = pos.side;
      this.location = pos.location;
      this.arrowDistance = pos.arrowDistance;
    },

    handleResize() {
      Vue.nextTick(() => {
        this.size = new Size(this.$el.offsetWidth, this.$el.offsetHeight);
        // padding is required to be equal on all sides
        const child = this.$el.children[0];
        this.innerPadding = child
          ? (this.size.width +
              this.size.height -
              (child.offsetWidth + child.offsetHeight)) /
            4
          : 0;
        this.updatePosition();
      });
    },
  },
};
</script>
