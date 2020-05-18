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
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-elementAdminForm">
    <div v-if="error">{{ error }}</div>
    <div class="tui-elementAdminForm__action">
      <Dropdown position="bottom-right">
        <template v-slot:trigger="{ toggle, isOpen }">
          <a href="#" @click.prevent="toggle">
            <ActivityActionsIcon
              :alt="$str('activity_action_options', 'mod_perform')"
              :title="$str('activity_action_options', 'mod_perform')"
              size="200"
            />
          </a>
        </template>
        <DropdownItem @click="remove">{{
          $str('section_delete_element', 'mod_perform')
        }}</DropdownItem>
      </Dropdown>
    </div>
    <slot name="content" />
  </div>
</template>

<script>
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import ActivityActionsIcon from 'mod_perform/components/icons/ActivityActions';
export default {
  components: {
    Dropdown,
    DropdownItem,
    ActivityActionsIcon,
  },

  props: {
    type: Object,
    error: String,
  },

  methods: {
    edit() {
      this.$emit('edit');
    },
    remove() {
      this.$emit('remove');
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform": [
    "activity_action_options",
    "section_delete_element"
  ]
  }
</lang-strings>
