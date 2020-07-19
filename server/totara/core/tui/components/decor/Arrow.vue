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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
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
