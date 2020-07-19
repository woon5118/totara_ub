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

  @author Arshad Anwer <arshad.anwer@totaralearning.com>
  @package totara_core
-->

<script>
import ResizeObserver from 'totara_core/polyfills/ResizeObserver';
import { throttle } from 'totara_core/util';

export default {
  props: {
    /**
     * Unique identifier of each slot when handleDataLoop is enabled
     **/
    uniqueKey: {
      type: [String, Number],
    },
  },

  data() {
    return {
      hasInitial: true,
      resizeObserver: null,
    };
  },

  mounted() {
    this.dispatchSizeChange();

    if (typeof ResizeObserver !== 'undefined') {
      this.resizeObserver = new ResizeObserver(
        throttle(this.dispatchSizeChange, 150)
      );
      this.resizeObserver.observe(this.$el);
    }
  },

  beforeDestroy() {
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
      this.resizeObserver = null;
    }
  },

  methods: {
    /**
     * Get item offset height
     * @returns {Number}
     */
    $_getHeightSize() {
      return this.$el ? this.$el.offsetHeight : 0;
    },

    /**
     * Emit size change event
     */
    dispatchSizeChange() {
      this.$emit(
        'resize',
        this.uniqueKey,
        this.$_getHeightSize(),
        this.hasInitial
      );
      this.hasInitial = false;
    },
  },

  render() {
    return this.$scopedSlots.default();
  },
};
</script>
