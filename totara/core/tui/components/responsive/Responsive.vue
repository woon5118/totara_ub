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
  <div class="tui-responsive">
    <slot :currentBoundaryName="currentBoundaryName" />
  </div>
</template>

<script>
import ResizeObserver from 'totara_core/polyfills/ResizeObserver';
import { throttle } from 'totara_core/util';

export default {
  props: {
    /**
     * Takes an Array Objects, where each Object provides an Array of pixel
     * `boundaries` in the form [lowerLimit, upperLimit] and a `name` to
     * describe the set of boundaries when a resize event should be emitted.
     **/
    breakpoints: {
      type: Array,
      default: () => [],
    },

    /**
     * Time delay before firing resize event. The event will be fired after the
     * time limit, not before and not in between. Time is in milliseconds.
     **/
    resizeThrottleTime: {
      type: Number,
      default: 200,
    },
  },

  data() {
    return {
      /**
       * Reference to ResizeObserver so we can clean up during unmount()
       **/
      resizeObserverRef: null,

      /**
       * Current boundary name passed as a slot prop for use within a template,
       * this offers simplified usage for basic components
       **/
      currentBoundaryName: null,
    };
  },

  computed: {
    /**
     * Returns the breakpoint with the highest upper boundary value, for
     * handling cases where no breakpoint is matched.
     *
     * @return {Int} largestBreakpoint
     **/
    largestBreakpoint() {
      let largestBreakpoint = this.breakpoints[0];
      this.breakpoints.map(function(breakpoint) {
        if (breakpoint.boundaries[0] > largestBreakpoint.boundaries[0]) {
          largestBreakpoint = breakpoint;
        }
      });
      return largestBreakpoint;
    },
  },

  mounted() {
    // when mounted, create a resize observer to detect changes in dimensions,
    // this will facilitate responsiveness to a finer level than relying solely
    // on viewport width. this technique is referred to as a 'container query'
    // and is useful when you want to switch between layouts inside a narrow
    // column, for example
    if (this.$el instanceof Element && this.breakpoints.length) {
      // wrap our resizing in a throttle method to prevent excessive execution
      const resize = throttle(
        entries => {
          // try to find a breakpoint match from supplied breakpoints. an
          // element width may be wider than supplied breakpoint values, in
          // which case use the largest available value
          let boundaryName =
            this.getBoundaryName(entries) || this.largestBreakpoint.name;

          // update current boundary so it can be passed as a slot prop
          this.currentBoundaryName = boundaryName;

          // notify parent  components of the change
          this.$emit('responsive-resize', boundaryName);
        },
        this.resizeThrottleTime,
        { leading: true, trailing: false }
      );

      this.resizeObserverRef = new ResizeObserver(resize);
      this.resizeObserverRef.observe(this.$el);
    }
  },

  unmounted() {
    // clean up ahead of garbage collection as there may be multiple Responsive
    // components on the page
    if (this.$el instanceof Element && this.resizeObserverRef) {
      this.resizeObserverRef.unobserve(this.$el);
    }
  },

  methods: {
    /**
     * Returns the `name` property of the breakpoint whose boundaries match the
     * width of the observed element. If no match is found, returns undefined.
     **/
    getBoundaryName: function(entries) {
      let boundaryName;
      this.breakpoints.map(function(breakpoint) {
        if (
          entries[0].contentRect.width > breakpoint.boundaries[0] &&
          entries[0].contentRect.width < breakpoint.boundaries[1]
        ) {
          boundaryName = breakpoint.name;
        }
      });
      return boundaryName;
    },
  },
};
</script>
