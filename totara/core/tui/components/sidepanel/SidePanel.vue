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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @package totara_core
-->

<template>
  <div
    ref="sidePanel"
    class="tui-sidePanel"
    :class="{
      'tui-sidePanel--animated': animated,
      'tui-sidePanel--overflows': overflows,
      'tui-sidePanel--sticky': sticky,
      'tui-sidePanel--open': isOpen && !closing,
      'tui-sidePanel--closed': !isOpen && !opening,
      'tui-sidePanel--opening': opening,
      'tui-sidePanel--closing': closing,
      'tui-sidePanel--ltr': direction === 'ltr',
      'tui-sidePanel--rtl': direction === 'rtl',
    }"
    :style="{
      maxHeight: maxHeight + 'px',
    }"
  >
    <div
      v-if="direction === 'ltr'"
      ref="sidePanel__inner"
      class="tui-sidePanel__inner"
    >
      <div ref="sidePanel__content" class="tui-sidePanel__content">
        <slot ref="removableContent" />
      </div>
    </div>

    <ButtonIcon
      v-if="showButtonControl"
      :disabled="opening || closing"
      :aria-label="$str(isOpen ? 'collapse' : 'expand', 'moodle')"
      :aria-expanded="isOpen ? 'true' : 'false'"
      class="tui-sidePanel__outsideClose"
      @click.prevent="isOpen ? collapse() : expand()"
    >
      <CollapseIcon v-if="isOpen" />
      <ExpandIcon v-else />
    </ButtonIcon>

    <div
      v-if="direction === 'rtl'"
      ref="sidePanel__inner"
      class="tui-sidePanel__inner"
    >
      <div ref="sidePanel__content" class="tui-sidePanel__content">
        <slot ref="removableContent" />
      </div>
    </div>
  </div>
</template>

<script>
import { waitForTransitionEnd } from 'totara_core/dom/transitions';
import CollapseIcon from 'totara_core/components/icons/common/Collapse';
import ExpandIcon from 'totara_core/components/icons/common/Expand';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import { throttle } from 'totara_core/util';

const isIE = document.body.classList.contains('ie');

export default {
  components: {
    ButtonIcon,
    CollapseIcon,
    ExpandIcon,
  },

  props: {
    /**
     * Whether the SidePanel should is intended to be opened from the left or
     * right side of a page. Expected values are 'ltr' and 'rtl'
     **/
    direction: {
      type: String,
      default: 'ltr',
      validator: str => ['ltr', 'rtl'].includes(str),
    },

    /**
     * Whether the SidePanel should be open when it is first rendered
     **/
    initiallyOpen: {
      type: Boolean,
      default: false,
    },

    /**
     * Whether transition lifecycles should be managed for CSS-based animations
     **/
    animated: {
      type: Boolean,
      default: true,
    },

    /**
     * Whether the SidePanel should remain wholly in the viewport when a long
     * page is scrolled
     **/
    sticky: {
      type: Boolean,
      default: true,
    },

    /**
     * Whether to set a CSS max-height value that is not `initial`
     **/
    limitHeight: {
      type: Boolean,
      default: true,
    },

    /**
     * Pixel based value that the SidePanel will respect with short viewports
     **/
    minHeight: {
      type: Number,
      default: 250, // assumed a px based calculation
    },

    /**
     * Whether the SidePanel's height should grow when scrolling, up to a max
     * height of the current size of the viewport
     **/
    growHeightOnScroll: {
      type: Boolean,
      default: true,
    },

    /**
     * Whether the SidePanel inner container should invoke a scrollbar if its
     * contents exceed its available height
     **/
    overflows: {
      type: Boolean,
      default: true,
    },

    /**
     * Whether to render the expand/collapse SidePanel toggle control
     **/
    showButtonControl: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      /**
       * Toggle value indicating the SidePanel is completely open or closed.
       * Includes after an expand or collapse transition lifecycle has completed,
       * or immediately in the case where no transition is used
       **/
      isOpen: false,

      /**
       * Toggle value indicating the SidePanel is currently moving between a
       * completely open state to a completely closed state
       **/
      closing: false,

      /**
       * Toggle value indicating the SidePanel is currently moving between a
       * completely closed state to a completely open state
       **/
      opening: false,

      /**
       * Internal data value used when the `limitHeight` prop is set to `true`
      height: 'auto',
       **/

      /**
       * Internal data value used when the `limitHeight` prop is set to `true`
       **/
      maxHeight: 'initial',

      /**
       * Method invoked during the window.onresize event, when the `limitHeight`
       * prop is set to `true`, which recalculates the `height` internal data
       * value in response to viewport resizing
       **/
      resizeHandler: null,

      /**
       * Let's throttle viewport resize calculations to improve performance
       **/
      resizeThrottleTime: 100,

      /**
       * Method invoked during the window.onscroll event, when the
       * `growHeightOnScroll` prop is set to `true`, which recalculates the
       * `height` internal data value
       **/
      scrollHandler: null,

      /**
       * Let's throttle viewport scroll calculations to improve performance
       **/
      scrollThrottleTime: 200,
    };
  },

  computed: {
    shouldBeOpen() {
      return this.initiallyOpen;
    },
  },

  watch: {
    shouldBeOpen(open) {
      if (open) {
        this.expand();
      } else {
        this.collapse();
      }
    },
  },

  mounted() {
    if (this.$refs.sidePanel instanceof Element) {
      // handle height calculations when we need to, or can, limit height by
      // available space in viewport
      if (this.limitHeight && !isIE) {
        this.doResize();

        this.resizeHandler = throttle(
          () => {
            this.$_resize();
          },
          this.resizeThrottleTime,
          { leading: false, trailing: true }
        );
        window.addEventListener('resize', this.resizeHandler);
      }

      // handle height calculations more acutely when we need to have fluid
      // height when the viewport is scrolled
      if (this.growHeightOnScroll) {
        this.scrollHandler = throttle(
          () => {
            this.$_scroll();
          },
          this.scrollThrottleTime,
          { leading: false, trailing: true }
        );

        window.addEventListener('scroll', this.scrollHandler, {
          passive: true,
        });
      }
    }

    // expand as soon as the SidePanel mounts, if configured so
    if (this.shouldBeOpen) {
      this.expand();
    }
  },

  beforeDestroy() {
    // clean up event listeners
    if (this.resizeHandler) {
      window.removeEventListener('resize', this.resizeHandler);
    }

    if (this.scrollHandler) {
      window.removeEventListener('scroll', this.scrollHandler);
    }
  },

  methods: {
    expand() {
      if (this.isOpen || this.opening || this.closing) {
        return;
      }
      this.opening = true;
      this.$emit('sidepanel-expanding');

      this.$_animate().then(() => {
        this.opening = false;
        this.isOpen = true;

        this.$emit('sidepanel-expanded');
      });
    },

    collapse() {
      if (!this.isOpen || this.opening || this.closing) {
        return;
      }

      this.closing = true;
      this.$emit('sidepanel-collapsing');

      this.$_animate().then(() => {
        this.closing = false;
        this.isOpen = false;

        this.$emit('sidepanel-collapsed');
      });
    },

    async $_animate() {
      if (this.animated) {
        // only interested in tracking transitions on content holding containers
        const transitionEls = [
          this.$refs.sidePanel__inner,
          this.$refs.sidePanel__content,
        ].filter(Boolean);

        await waitForTransitionEnd(transitionEls);
      }
    },

    $_resize() {
      this.doResize();
      return;
    },

    $_scroll() {
      // remove pixel calculations if we shouldn't be growing on scroll
      if (!this.growHeightOnScroll) {
        this.maxHeight = 'initial';
        return;
      }

      // otherwise recalculate heights
      this.doResize();
    },

    doResize() {
      let rect = this.$refs.sidePanel.getBoundingClientRect(),
        parentRect = this.$refs.sidePanel.parentNode.getBoundingClientRect(),
        newMaxHeight,
        positionTop,
        positionBottom;

      // is the window scrolled up to the top? if so, allow SidePanel's max
      // height to be equal to the height of the window minus its relative top
      // position from 0,0 coords within window
      if (window.scrollY === 0) {
        positionTop = true;
        newMaxHeight = window.innerHeight - rect.top;
      }

      // is the window scrolled to the bottom? if so, allow SidePanel's max
      // height to be equal to its current max height plus its relative top
      // position from 0,0 coords within window
      if (
        !positionTop &&
        window.innerHeight + window.scrollY >= document.body.offsetHeight
      ) {
        positionBottom = true;
        newMaxHeight =
          window.innerHeight - (window.innerHeight - parentRect.bottom);
      }

      // if the window is scrolled somewhere between the top and bottom scrollY
      // position, determine a suitable new max height
      if (!positionTop && !positionBottom) {
        // start with full window height assumption
        newMaxHeight = window.innerHeight;

        // then, if the parent bottom coords have scrolled into view, remove
        // the parent rect.bottom value from the height of the SidePanel
        if (parentRect.bottom < window.innerHeight) {
          newMaxHeight = parentRect.bottom;
        }
      }

      // set the new SidePanel max height
      this.maxHeight = newMaxHeight;
      return;
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "expand",
    "collapse"
  ]
}
</lang-strings>
