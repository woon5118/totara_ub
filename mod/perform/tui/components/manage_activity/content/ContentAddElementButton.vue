<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
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
    addElementPlugin(plugin) {
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
