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
  <div
    v-if="this.$slots['default']"
    class="tui-separator tui-separator--wrapped"
    :class="[
      thick ? 'tui-separator--thick' : '',
      spread ? 'tui-separator--spread' : '',
    ]"
  >
    <span class="tui-separator-content">
      <slot />
    </span>
    <hr class="sr-only" />
  </div>
  <hr
    v-else
    class="tui-separator tui-separator-rule"
    :class="[
      thick ? 'tui-separator--thick' : '',
      spread ? 'tui-separator--spread' : '',
    ]"
  />
</template>
<script>
export default {
  props: {
    /**
     * When provided, a modifier className is added to the element that
     * references a SCSS variable to set a thicker visible horizontal rule
     **/
    thick: {
      type: Boolean,
      default: function() {
        return false;
      },
    },

    /**
     * When provided, a modifier className is added to the element that
     * references a SCSS variable to set increased vertical margins
     **/
    spread: {
      type: Boolean,
      default: function() {
        return false;
      },
    },
  },
};
</script>
