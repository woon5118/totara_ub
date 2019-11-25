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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module editor_weka
-->

<template>
  <div class="tui-editorWeka-linkBlock" :data-url="attrs.url">
    <Dropdown>
      <template v-slot:trigger="{ toggle, isOpen }">
        <div @click.capture.prevent="toggle">
          <LinkBlockInner :attrs="attrs" />
        </div>
        <div class="tui-editorWeka-linkBlock__btn-wrapper">
          <ButtonIcon
            :styleclass="{ small: true, transparent: true }"
            class="tui-editorWeka-linkBlock__btn"
            :aria-expanded="isOpen.toString()"
            :aria-label="
              $str('actions_menu_for', 'editor_weka', attrs.title || attrs.url)
            "
            @click="toggle"
          >
            <More />
          </ButtonIcon>
        </div>
      </template>
      <DropdownButton @click="open">
        {{ $str('go_to_link', 'editor_weka') }}
      </DropdownButton>
      <DropdownButton @click="edit">
        {{ $str('edit', 'moodle') }}
      </DropdownButton>
      <DropdownButton @click="toLink">
        {{ $str('display_as_text', 'editor_weka') }}
      </DropdownButton>
      <DropdownButton @click="remove">
        {{ $str('remove', 'moodle') }}
      </DropdownButton>
    </Dropdown>
  </div>
</template>

<script>
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownButton from 'tui/components/dropdown/DropdownButton';
import LinkBlockInner from 'tui/components/json_editor/nodes/LinkBlock';
import More from 'tui/components/icons/common/More';

export default {
  components: {
    ButtonIcon,
    Dropdown,
    DropdownButton,
    LinkBlockInner,
    More,
  },

  extends: BaseNode,

  methods: {
    open() {
      window.open(this.attrs.url);
    },

    edit() {
      this.context.editCard(this.getRange);
    },

    toLink() {
      const url = this.attrs.url;
      this.context.replaceWithTextLink(this.getRange, { url });
    },

    remove() {
      this.$emit('remove');
    },
  },
};
</script>

<lang-strings>
{
  "editor_weka": ["display_as_text", "go_to_link", "actions_menu_for"],
  "moodle": ["edit", "remove"]
}
</lang-strings>
