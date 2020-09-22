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

  @author Dave Wallace <dave.wallace@totaralearning.com>
  @module tui
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
      maxHeight: limitHeight ? maxHeight + 'px' : 'initial',
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
      <div
        ref="sidePanel__content"
        class="tui-sidePanel__content"
        :style="{ width: contentWidth }"
      >
        <slot ref="removableContent" />
      </div>
    </div>
  </div>
</template>

<script>
import { waitForTransitionEnd } from 'tui/dom/transitions';
import CollapseIcon from 'tui/components/icons/Collapse';
import ExpandIcon from 'tui/components/icons/Expand';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import { throttle } from 'tui/util';

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
     * Whether the SidePanel's inner content should have a fixed width when its state is expanding or collapsing
     * When set to true, a fixed-width will be applied, preventing reflow of SidePanel contents during transitions
     **/
    fixContentWidth: {
      type: Boolean,
      default: true,
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
       * Width of the content preventing reflow of SidePanel contents during transitions
       **/
      contentWidth: 'auto',

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

      this.contentWidth = 'auto';

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

      this.contentWidth = this.getContentWidth();

      this.$_animate().then(() => {
        this.closing = false;
        this.isOpen = false;

        this.$emit('sidepanel-collapsed');
      });
    },

    getContentWidth() {
      if (this.fixContentWidth)
        return (
          this.$refs.sidePanel__content.getBoundingClientRect().width + 'px'
        );
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

<style lang="scss">
.tui-sidePanel {
  position: relative;
  display: flex;
  align-items: center;
  height: 100%;
  overflow: hidden;

  &--animated {
    transition: max-height var(--transition-sidepanel-scrollsnap-duration)
      var(--transition-sidepanel-scrollsnap-function);
  }

  &--sticky {
    position: sticky;
    top: 0;

    .ie & {
      position: relative;
      top: auto;
    }
  }

  // inner content alignment
  &--rtl,
  .dir-rtl .tui-sidePanel--ltr & {
    justify-content: flex-end;
  }
  &--ltr,
  .dir-rtl .tui-sidePanel--rtl & {
    justify-content: flex-start;
  }

  /**
   * Close button, somewhat complicated by the SidePanel being configurably
   * bi-directional and both of those directions also requiring RTL support
   **/
  @mixin attrs-from-right() {
    margin-right: -1px;
    margin-left: 4px; /* ensure focus shadow is not cut off by container */
    border-right-width: 0;
    border-left-width: 1px;
    border-radius: var(--btn-radius) 0 0 var(--btn-radius);

    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      border-right-width: 0;
      border-left-width: 1px;
      box-shadow: -2px 1px 4px 0 rgba(0, 0, 0, 0.2);
    }

    // FlexIcon
    .tui-iconBtn__icon {
      transform: rotate(90deg);
    }
  }
  @mixin attrs-from-left() {
    margin-right: 4px; /* ensure focus shadow is not cut off by container */
    margin-left: -1px;
    border-right-width: 1px;
    border-left-width: 0;
    border-radius: 0 var(--btn-radius) var(--btn-radius) 0;

    &:hover,
    &:active,
    &:active:focus,
    &:active:hover {
      border-right-width: 1px;
      border-left-width: 0;
      box-shadow: 2px 1px 4px 0 rgba(0, 0, 0, 0.2);
    }
    // FlexIcon
    .tui-iconBtn__icon {
      transform: rotate(-90deg);
    }
  }

  &__outsideClose {
    .ie & {
      // height, position and scrolling will degrade in IE11, so the toggle
      // button needs a more appropriate location than "the middle" of the
      // SidePanel, which could be very tall in IE11
      align-self: flex-start;
      max-width: 30px;
      margin-top: var(--gap-8);
    }

    flex-grow: 0;
    min-width: 30px;
    height: auto;
    padding: var(--gap-6) var(--gap-1);
    background-color: var(--color-neutral-3);
    border-color: var(--color-neutral-5);

    .tui-sidePanel--rtl &,
    .dir-rtl .tui-sidePanel--ltr & {
      @include attrs-from-right();
    }

    .tui-sidePanel--ltr &,
    .dir-rtl .tui-sidePanel--rtl & {
      @include attrs-from-left();
    }
  }

  /**
   * A wrapper for content container, which helps with transitions on width
   * while overflowing content is still visible, and providing whitespace
   * between content and the edges of the SidePanel
   **/
  &__inner {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    flex-shrink: 1;
    width: 100%;
    height: 100%;
    max-height: 100%;
    background-color: var(--color-neutral-3);
    border: 1px solid var(--color-neutral-5);

    .tui-sidePanel--open.tui-sidePanel--overflows & {
      overflow-y: auto;
    }

    .tui-sidePanel--closed & {
      max-width: 1px;
      padding-right: 0;
      padding-left: 0;
    }
  }

  /**
   * Transitioned container for arbitrary SidePanel content
   **/
  &__content {
    height: 100%;
    overflow: hidden;

    .ie & {
      height: 100%;
    }

    .tui-sidePanel--closed &,
    .tui-sidePanel--closing & {
      opacity: 0;
    }

    .tui-sidePanel--closed & {
      visibility: hidden;
    }

    .tui-sidePanel--open &,
    .tui-sidePanel--opening & {
      opacity: 1;
    }

    .tui-sidePanel--animated & {
      transition: opacity var(--transition-sidepanel-content-duration)
        var(--transition-sidepanel-content-function);
    }

    .tui-sidePanel--open.tui-sidePanel--overflows & {
      overflow-y: auto;
    }
  }
}
</style>
