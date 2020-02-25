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
  <Modal size="sheet" :aria-labelledby="$id('title')">
    <ModalContent :title="section.title">
      <div class="tui-addElementWrapper">
        <Dropdown>
          <template v-slot:trigger="{ toggle }">
            <ButtonIcon
              :text="$str('perform:section:add_element', 'mod_perform')"
              @click.prevent="toggle"
            >
              <AddIcon size="200" />
            </ButtonIcon>
          </template>
          <DropdownItem
            v-for="plugin in elementPlugins"
            :key="plugin.plugin_name"
          >
            {{ plugin.name }}
          </DropdownItem>
        </Dropdown>
      </div>
    </ModalContent>
  </Modal>
</template>

<script>
import AddIcon from 'totara_core/components/icons/common/Add';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import performElementPluginsQuery from 'mod_perform/graphql/element_plugins';
import sectionDetailQuery from 'mod_perform/graphql/section_details';

export default {
  components: {
    AddIcon,
    ButtonIcon,
    Modal,
    ModalContent,
    Dropdown,
    DropdownItem,
  },
  data() {
    return {
      section: { title: '' },
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
    section: {
      query: sectionDetailQuery,
      variables() {
        return { section_id: 1 };
      },
      update: data => data.mod_perform_section,
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "perform:section:add_element"
    ]
  }
</lang-strings>
