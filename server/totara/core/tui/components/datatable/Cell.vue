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
    class="tui-dataTableCell"
    :class="[
      align && 'tui-dataTableCell--align-' + align,
      heavy && 'tui-dataTableCell--heavy',
      repeatedHeader && 'tui-dataTableCell--repeatedHeader',
      size && 'tui-dataTableCell--size_' + size,
      valign && 'tui-dataTableCell--valign-' + valign,
    ]"
    role="cell"
  >
    <span v-if="columnHeader" class="tui-dataTableCell__label" aria-hidden>
      {{ columnHeader }}
    </span>
    <div class="tui-dataTableCell__content">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    align: {
      type: String,
      validator: val => ['start', 'center', 'end'].indexOf(val) !== -1,
    },
    columnHeader: String,
    heavy: Boolean,
    repeatedHeader: Boolean,
    size: String,
    valign: {
      type: String,
      validator: val => ['start', 'center', 'end'].indexOf(val) !== -1,
    },
  },
};
</script>
