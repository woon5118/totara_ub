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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-basket">
    <div class="tui-basket__status">
      <div class="tui-basket__selected">
        {{ $str('selected', 'totara_core') }}:
        <span class="tui-basket__selectedCount">{{ selectedCount }}</span>
      </div>
      <slot name="status" :empty="empty" />
    </div>
    <div class="tui-basket__actions">
      <slot name="actions" :empty="empty" />
      <Dropdown
        v-if="bulkActions && bulkActions.length > 0 && !singleAction"
        position="bottom-right"
      >
        <template v-slot:trigger="{ toggle, isOpen }">
          <Button
            :aria-expanded="isOpen ? 'true' : 'false'"
            :caret="true"
            :styleclass="{ primary: true, small: true }"
            :text="$str('bulkactions')"
            :disabled="empty"
            @click="toggle"
          />
        </template>
        <DropdownItem
          v-for="(action, i) in bulkActions"
          :key="i"
          @click="action.action"
        >
          {{ action.label }}
        </DropdownItem>
      </Dropdown>
      <Button
        v-else-if="singleAction"
        :styleclass="{ primary: true, small: true }"
        :text="singleAction.label"
        :disabled="empty"
        @click="singleAction.action"
      />
    </div>
  </div>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';

export default {
  components: {
    Button,
    Dropdown,
    DropdownItem,
  },

  props: {
    items: {
      type: Array,
      required: true,
    },

    bulkActions: Array,

    showClear: Boolean,
  },

  computed: {
    selectedCount() {
      return this.items.length;
    },

    empty() {
      return this.items.length === 0;
    },

    singleAction() {
      return this.bulkActions && this.bulkActions.length === 1
        ? this.bulkActions[0]
        : false;
    },
  },
};
</script>

<lang-strings>
{
  "moodle": ["bulkactions"],
  "totara_core": ["selected"]
}
</lang-strings>
