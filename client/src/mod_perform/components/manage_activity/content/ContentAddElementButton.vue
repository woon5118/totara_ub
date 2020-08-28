<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
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
      <span
        tabindex="-1"
        role="menugroup"
        class="tui-dropdownItem tui-performEditSectionContentAddElement__dropDownGroupItem"
      >
        {{ $str('section_dropdown_question_elements', 'mod_perform') }}
      </span>
      <DropdownItem
        v-for="plugin in questionElement"
        :key="plugin.plugin_name"
        class="tui-performEditSectionContentAddElement__dropDownItem"
        @click="addElementPlugin(plugin)"
      >
        {{ plugin.name }}
      </DropdownItem>
      <span
        tabindex="-1"
        role="menugroup"
        class="tui-dropdownItem tui-performEditSectionContentAddElement__dropDownGroupItem"
      >
        {{ $str('section_dropdown_other_elements', 'mod_perform') }}
      </span>
      <DropdownItem
        v-for="plugin in otherElement"
        :key="plugin.plugin_name"
        class="tui-performEditSectionContentAddElement__dropDownItem"
        @click="addElementPlugin(plugin)"
      >
        {{ plugin.name }}
      </DropdownItem>
    </Dropdown>
  </div>
</template>
<script>
import AddIcon from 'tui/components/icons/Add';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
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
  computed: {
    questionElement: function() {
      return this.elementPlugins.filter(function(plugin) {
        if (plugin.group === '1') {
          return plugin;
        }
      });
    },
    otherElement: function() {
      return this.elementPlugins.filter(function(plugin) {
        if (plugin.group === '2') {
          return plugin;
        }
      });
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
      "section_add_element",
      "section_dropdown_other_elements",
      "section_dropdown_question_elements"
    ]
  }
</lang-strings>
