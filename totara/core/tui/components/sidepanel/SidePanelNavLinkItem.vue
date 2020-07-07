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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <li
    v-focus-within
    class="tui-sidePanelNavLinkItem"
    :class="{ 'tui-sidePanelNavLinkItem--active': activeItem }"
  >
    <a
      :href="url"
      class="tui-sidePanelNavLinkItem__action"
      :aria-current="activeItem ? 'location' : null"
      @click="$emit('select', { action: url, id: id })"
    >
      {{ text }}
    </a>

    <div v-if="$scopedSlots.default" class="tui-sidePanelNavLinkItem__side">
      <slot />
    </div>
  </li>
</template>

<script>
export default {
  props: {
    active: [Boolean, Number, String],
    id: {
      type: [Number, String],
      required: true,
    },
    text: String,
    url: String,
  },

  computed: {
    /**
     * Check if this is the active item
     *
     * @return {Bool}
     */
    activeItem() {
      if (this.active == this.id) {
        return true;
      }
      return false;
    },
  },
};
</script>
