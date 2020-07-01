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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-performEditSectionContentAddElement">
    <Dropdown :separator="true">
      <template v-slot:trigger="{ toggle, isOpen }">
        <ButtonIcon
          :aria-expanded="isOpen ? 'true' : 'false'"
          :aria-label="$str('section_add_element', 'mod_perform')"
          :text="$str('section_add_element', 'mod_perform')"
          :styleclass="{ small: true }"
          :caret="true"
          @click.prevent="toggle"
        >
          <AddIcon size="200" />
        </ButtonIcon>
      </template>
      <DropdownItem
        v-for="plugin in elementPlugins"
        :key="plugin.plugin_name"
        @click="addElementPlugin(plugin)"
      >
        {{ plugin.name }}
      </DropdownItem>
    </Dropdown>
  </div>
</template>
<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import performElementPluginsQuery from 'mod_perform/graphql/element_plugins';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    Dropdown,
    DropdownItem,
  },
  data() {
    return {
      isElementsVisible: false,
      elementPlugins: [],
    };
  },
  apollo: {
    elementPlugins: {
      query: performElementPluginsQuery,
      variables() {
        return [];
      },
      update: data => data.mod_perform_element_plugins,
    },
  },
  methods: {
    showElements() {
      this.isElementsVisible = true;
    },
    addElementPlugin(plugin) {
      this.isElementsVisible = false;
      this.$emit('add-element-item', plugin);
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "section_element_questions",
      "section_add_element"
    ]
  }
</lang-strings>
