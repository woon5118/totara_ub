<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_core
-->

<template>
  <div
    v-focus-within
    class="tui-card"
    :class="[
      clickable && 'tui-card--clickable',
      noBorder && 'tui-card--noBorder',
      hasShadow && 'tui-card--hasShadow',
    ]"
    @click="clickCard"
  >
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    clickable: {
      type: Boolean,
    },
    hasShadow: {
      type: Boolean,
    },
    noBorder: {
      type: Boolean,
    },
  },
  methods: {
    clickCard() {
      if (!this.clickable) return;
      this.$emit('click');
    },
  },
};
</script>
