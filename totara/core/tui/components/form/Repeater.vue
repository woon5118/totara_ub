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
    :id="uid"
    class="tui-repeater"
    :class="[noSpacing && 'tui-repeater--noSpacing']"
    aria-live="polite"
  >
    <template v-for="(row, index) in rows">
      <div :key="index" class="tui-repeater__row">
        <slot :row="row" :index="index" />
        <ButtonIcon
          v-if="showDeleteIcon(index)"
          :aria-label="$str('delete', 'moodle')"
          :styleclass="{ small: true, stealth: true }"
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
        :aria-controls="uid"
        :styleclass="{ small: true, circle: true }"
        :disabled="disabled"
        @click="$emit('add')"
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
      default: Infinity,
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
    noSpacing: {
      type: Boolean,
      default: false,
    },
  },

  methods: {
    showDeleteIcon(index) {
      if (!this.deleteIcon) {
        return false;
      }
      return this.allowDeletingFirstItems
        ? this.rows.length > this.minRows
        : index >= this.minRows;
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
