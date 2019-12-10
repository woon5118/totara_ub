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
  <a
    v-if="href"
    :href="href"
    class="tui-tag tui-tag__content"
    role="tag-link"
    >{{ text }}</a
  >

  <div v-else class="tui-tag" :class="{ 'tui-tag--primary': primary }">
    <span
      class="tui-tag__content"
      :class="{ 'tui-tag__content--withButton': $slots.button }"
      >{{ text }}</span
    >
    <slot name="button" />
  </div>
</template>

<script>
export default {
  props: {
    primary: Boolean,
    text: {
      type: String,
      required: true,
    },
    href: String,
  },
};
</script>
