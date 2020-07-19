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
  <a
    class="tui-actionLink"
    :href="disabled ? false : href"
    :class="{
      'tui-actionLink--prim': styleclass.primary,
      'tui-actionLink--small': styleclass.small,
      'tui-formBtn--srOnly': styleclass.srOnly,
    }"
    @click="handleClick"
  >
    {{ text }}
  </a>
</template>

<script>
export default {
  props: {
    styleclass: {
      default: () => ({
        primary: false,
        small: false,
        srOnly: false,
      }),
      type: Object,
    },
    disabled: Boolean,
    href: {
      required: true,
      type: String,
    },
    text: {
      required: true,
      type: String,
    },
  },

  methods: {
    handleClick(e) {
      if (this.disabled) {
        e.preventDefault();
        e.stopPropagation();
        return;
      }
      this.$emit('click', e);
    },
  },
};
</script>
