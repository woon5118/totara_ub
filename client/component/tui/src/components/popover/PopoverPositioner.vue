<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module tui
-->

<template>
  <transition
    :name="transition && 'tui-popoverPositioner--transition-' + transition"
  >
    <div
      v-show="shouldBeOpen"
      class="tui-popoverPositioner"
      :class="[
        transition && 'tui-popoverPositioner--transition-' + transition,
        isFixed && 'tui-popoverPositioner--fixed',
      ]"
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
  getBox,
  getContainingBlockInfo,
} from 'tui/dom/position';
import { getClosestScrollable } from 'tui/dom/scroll';
import { position } from 'tui/lib/popover';
import { Point, Size, Rect } from 'tui/geometry';

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
    // allow popover to slide along the side in preference to moving to different side
    // if this is true, it will still slide if it can't fit on any side
    preferSlide: Boolean,
  },

  data() {
    return {
      location: new Point(0, 0),
      size: new Size(0, 0),
      innerPadding: 0,
      shouldBeOpen: false,
      computedSide: null,
      arrowDistance: 0,
      isFixed: false,
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
    open: {
      immediate: true,
      handler(open) {
        if (open) {
          Vue.nextTick(() => {
            this.shouldBeOpen = true;
            this.$_setupOpen();
            this.handleResize();
          });
        } else {
          this.$_closeCleanup();
          this.shouldBeOpen = false;
        }
      },
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
    this.$_closeCleanup();
    this.resizeObserver.disconnect();
  },

  methods: {
    updatePosition() {
      if (!this.referenceElement) return;

      let refRect;
      let viewport = null;
      if (this.isFixed) {
        const containingBlock = getContainingBlockInfo(this.$el, {
          position: 'fixed',
        });
        if (process.env.NODE_ENV !== 'production') {
          const referenceContainingBlock = getContainingBlockInfo(
            this.referenceElement,
            { position: 'fixed' }
          );
          if (containingBlock.el != referenceContainingBlock.el) {
            console.warn(
              '[PopoverPositioner] Reference element and PopoverPositioner are not in the same containing block.'
            );
            console.log('Reference element', this.referenceElement);
            console.log(
              'Reference element containing block',
              referenceContainingBlock.el
            );
            console.log('PopoverPositioner', this.$el);
            console.log(
              'PopoverPositioner containing block',
              containingBlock.el
            );
          }
        }
        refRect = getBox(this.referenceElement).borderBox;
        viewport = new Rect(
          0,
          0,
          containingBlock.rect.width,
          containingBlock.rect.height
        );
      } else {
        if (
          process.env.NODE_ENV !== 'production' &&
          this.$el.offsetParent &&
          this.referenceElement.offsetParent != this.$el.offsetParent
        ) {
          console.warn(
            '[PopoverPositioner] Reference element and PopoverPositioner are not in the same offset parent.'
          );
          console.log('Reference element', this.referenceElement);
          console.log(
            'Reference element offset parent',
            this.referenceElement.offsetParent
          );
          console.log('PopoverPositioner', this.$el);
          console.log('PopoverPositioner offset parent', this.$el.offsetParent);
        }
        refRect = getOffsetRect(this.referenceElement);
        const offsetParentPosition = getDocumentPosition(
          this.referenceElement.offsetParent
        );
        viewport = getViewportRect().sub(offsetParentPosition);
      }

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

    $_setupOpen() {
      this.$_closeCleanup();
      this.isFixed = this.$_useFixedPositioning();
      if (this.isFixed) {
        this.scrollableContainers = [];
        let scrollable = getClosestScrollable(this.$el.parentNode);
        while (scrollable) {
          this.scrollableContainers.push(scrollable);
          scrollable.addEventListener('scroll', this.handleResize);
          scrollable = getClosestScrollable(scrollable.parentNode);
        }
      }
    },

    $_closeCleanup() {
      if (this.scrollableContainers) {
        this.scrollableContainers.forEach(x =>
          x.removeEventListener('scroll', this.handleResize)
        );
        this.scrollableContainers = null;
      }
    },

    $_useFixedPositioning() {
      return !!this.$el.closest('.tui-modalContent');
    },
  },
};
</script>
