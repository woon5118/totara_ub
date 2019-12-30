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
    class="tui-repeater"
    :class="[
      `tui-repeater--${align}`,
      `tui-repeater--${direction}`,
      noSpacing && 'tui-repeater--noSpacing',
    ]"
  >
    <template v-for="(row, index) in rows">
      <div
        :key="index"
        class="tui-repeater__row"
        :class="`tui-repeater__row--${direction}-${rowAlign}`"
      >
        <slot :row="row" :index="index" />
        <ButtonIcon
          v-if="deleteIcon && !hideDeleteIcon(index)"
          :aria-label="$str('delete', 'moodle')"
          :styleclass="{ small: true, transparent: true }"
          :disabled="disabled"
          @click="$emit('remove', row, index)"
        >
          <DeleteIcon />
        </ButtonIcon>
      </div>
    </template>
    <slot name="add">
      <ButtonIcon
        v-if="rows.length < maxRows"
        :aria-label="$str('add', 'moodle')"
        :styleclass="{ small: true, circle: true }"
        :disabled="disabled"
        @click.stop="$emit('add')"
      >
        <AddIcon />
      </ButtonIcon>
    </slot>
  </div>
</template>

<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import DeleteIcon from 'totara_core/components/icons/common/Delete';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    DeleteIcon,
  },
  props: {
    noSpacing: {
      type: Boolean,
      default: false,
    },
    rows: {
      type: Array,
      required: true,
    },
    minRows: {
      type: Number,
      default: 0,
    },
    maxRows: {
      type: Number,
      default: 10,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    deleteIcon: {
      type: Boolean,
      default: false,
    },
    allowDeletingFirstItems: {
      type: Boolean,
      default: true,
    },
    align: {
      type: String,
      default: 'start',
      validator: val => ['start', 'center', 'end'].includes(val),
    },
    rowAlign: {
      type: String,
      default: 'center',
      validator: val => ['start', 'center', 'end'].includes(val),
    },
    direction: {
      type: String,
      default: 'vertical',
      validator: val => ['horizontal', 'vertical'].includes(val),
    },
  },
  methods: {
    hideDeleteIcon(index) {
      return (
        (this.allowDeletingFirstItems && this.rows.length <= this.minRows) ||
        (!this.allowDeletingFirstItems && index < this.minRows)
      );
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "add",
    "delete"
  ]
}
</lang-strings>
