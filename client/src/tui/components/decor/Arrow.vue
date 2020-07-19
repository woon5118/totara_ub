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
  <div
    class="tui-arrow"
    :class="['tui-arrow--' + computedSide, 'tui-arrow--' + size]"
    :style="style"
  />
</template>

<script>
import { isRtl } from 'totara_core/i18n';

const relativeSide = {
  top: 'bottom',
  bottom: 'top',
  left: 'right',
  right: 'left',
};

export default {
  props: {
    // direction the arrow should point towards (top/bottom/left/right)
    side: String,
    // side relative to the reference - inverse of side. provide one or the other.
    relativeSide: String,
    // distance along side to place the arrow
    distance: [Number, String],
    size: {
      type: String,
      default: 'normal',
    },
  },

  computed: {
    style() {
      const side = this.computedSide;
      if (side == null || this.distance == null) {
        return {};
      }
      let posSide = 'top';
      if (side == 'top' || side == 'bottom') {
        posSide = isRtl() ? 'right' : 'left';
      }

      return {
        [posSide]: Math.round(this.distance) + 'px',
      };
    },

    computedSide() {
      return this.side || relativeSide[this.relativeSide];
    },
  },
};
</script>
