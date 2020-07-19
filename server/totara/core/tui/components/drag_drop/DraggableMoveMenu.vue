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
  <Dropdown v-if="availableLists && availableLists.length > 0">
    <template v-slot:trigger="{ toggle, isOpen }">
      <Button
        :aria-expanded="isOpen ? 'true' : 'false'"
        :styleclass="{ transparent: true }"
        class="tui-draggableMoveMenu__button"
        :text="$str('move_to_list', 'totara_core')"
        @click="toggle"
      />
    </template>
    <DropdownItem
      v-for="list in availableLists"
      :key="list.sourceId"
      @click="() => handleClick(list)"
    >
      {{ list.sourceName }}
    </DropdownItem>
  </Dropdown>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';

export default {
  components: {
    Dropdown,
    DropdownItem,
    Button,
  },

  props: {
    availableLists: {
      type: Array,
    },
  },

  methods: {
    handleClick(list) {
      this.$emit('move', list);
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": ["move_to_list"]
}
</lang-strings>
