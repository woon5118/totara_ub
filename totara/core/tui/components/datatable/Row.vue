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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <div
    v-focus-within="!inGroup"
    class="tui-dataTableRow"
    :class="{
      'tui-dataTableRow--colorOdd': colorOdd,
      'tui-dataTableRow--disabled': disabled,
      'tui-dataTableRow--inGroup': inGroup,
      'tui-dataTableRow--selected': selected,
      'tui-dataTableRow--borderTopFirstOff': borderTopHidden,
      'tui-dataTableRow--borderBottomLastOff': borderBottomHidden,
      'tui-dataTableRow--borderSeparatorOff': borderSeparatorHidden,
      'tui-dataTableRow--hoverOff': hoverOff,
    }"
    role="row"
  >
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    borderBottomHidden: Boolean,
    borderSeparatorHidden: Boolean,
    borderTopHidden: Boolean,
    colorOdd: Boolean,
    disabled: Boolean,
    hoverOff: Boolean,
    inGroup: Boolean,
    selected: Boolean,
  },
};
</script>
