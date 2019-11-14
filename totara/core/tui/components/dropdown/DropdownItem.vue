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
    :href="href || '#'"
    tabindex="-1"
    class="tui-dropdownItem"
    :class="{
      'tui-dropdownItem--disabled': disabled,
      'tui-dropdownItem--paddingless': paddingless,
      'tui-dropdownItem--small': small,
    }"
    :aria-disabled="disabled"
    :role="role"
    @click="clickItem"
  >
    <slot />
  </a>
</template>

<script>
export default {
  props: {
    role: {
      type: String,
      default: 'menuitem',
    },
    disabled: Boolean,
    href: String,
    paddingless: Boolean,
    isDropdown: Boolean,
    small: Boolean,
  },

  methods: {
    clickItem(e) {
      if (!this.href) {
        e.preventDefault();
        this.$emit('click');
        return;
      }

      if (this.disabled) {
        e.stopPropagation();
        e.preventDefault();
        return;
      }

      this.$emit('click');
    },
  },
};
</script>
