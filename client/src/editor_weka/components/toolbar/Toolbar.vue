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
  <div
    class="tui-editorWeka-toolbar"
    role="toolbar"
    :aria-label="$str('label_toolbar', 'editor_weka')"
  >
    <div class="tui-editorWeka-toolbar__group">
      <Dropdown :separator="false">
        <template v-slot:trigger="{ toggle, isOpen }">
          <ButtonIcon
            class="tui-editorWeka-toolbar__currentBlock tui-editorWeka-toolbar__button"
            :styleclass="{ toolbar: true, textFirst: true }"
            :text="activeBlockName"
            :aria-expanded="isOpen ? 'true' : 'false'"
            :aria-label="
              $str('format_as_blocktype_status', 'editor_weka', activeBlockName)
            "
            :disabled="!blockSelectorEnabled"
            @click="toggle()"
            @keydown.native="handleButtonKeydown"
            @focus.native="handleButtonFocus"
          >
            <FlexIcon icon="caret-down" size="100" />
          </ButtonIcon>
        </template>
        <DropdownItem
          v-for="(item, i) in blockItems"
          :key="i"
          :selected="item.active"
          :disabled="!item.active && !item.enabled"
          :small="true"
          @click="itemClick(item)"
        >
          {{ item.label.toString() }}
        </DropdownItem>
      </Dropdown>
    </div>
    <div
      v-for="(itemGroup, groupKey) in itemGroups"
      :key="groupKey"
      class="tui-editorWeka-toolbar__group"
    >
      <template v-for="(item, i) in itemGroup">
        <!-- dropdown toolbar item -->
        <Dropdown v-if="item.children" :key="i" :separator="false">
          <template v-slot:trigger="{ toggle }">
            <ToolbarButton
              class="tui-editorWeka-toolbar__button"
              :text="item.label.toString()"
              :active="item.active"
              :disabled="!item.enabled"
              @click="toggle"
              @keydown.native="handleButtonKeydown"
              @focus.native="handleButtonFocus"
            >
              <template slot="icon">
                <FlexIcon :icon="item.icon" />
              </template>
            </ToolbarButton>
          </template>
          <DropdownItem
            v-for="(child, j) in item.children"
            :key="j"
            :disabled="!child.enabled"
            @click="itemClick(child)"
          >
            <FlexIcon :icon="child.icon" />
            {{ child.label.toString() }}
          </DropdownItem>
        </Dropdown>
        <!-- popover toolbar component -->
        <Popover
          v-else-if="item.popover"
          :key="i"
          :triggers="['click']"
          :title="item.popover.title"
          @open-changed="openChanged(item, ...arguments)"
        >
          <template v-slot:trigger="{ isOpen }">
            <ToolbarButton
              class="tui-editorWeka-toolbar__button"
              :text="item.label.toString()"
              :selected="item.active"
              :disabled="!item.enabled"
              aria-haspopup="true"
              :aria-expanded="isOpen ? 'true' : 'false'"
              @keydown.native="handleButtonKeydown"
              @focus.native="handleButtonFocus"
            >
              <template slot="icon">
                <FlexIcon :icon="item.icon" />
              </template>
            </ToolbarButton>
          </template>
          <template v-slot:default="{ close }">
            <component :is="item.popover.component" @close="close" />
          </template>
        </Popover>
        <!-- regular toolbar button -->
        <ToolbarButton
          v-else
          :key="i"
          class="tui-editorWeka-toolbar__button"
          :text="item.label.toString()"
          :selected="item.active"
          :disabled="!item.enabled"
          @click="itemClick(item)"
          @keydown.native="handleButtonKeydown"
          @focus.native="handleButtonFocus"
        >
          <template slot="icon">
            <FlexIcon :icon="item.icon" />
          </template>
        </ToolbarButton>
      </template>
    </div>
  </div>
</template>

<script>
import { groupBy } from 'tui/util';
import { getTabbableElements } from 'tui/dom/focus';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import FlexIcon from 'tui/components/icons/FlexIcon';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import ToolbarButton from 'editor_weka/components/toolbar/ToolbarButton';
import Popover from 'tui/components/popover/Popover';

export default {
  components: {
    Dropdown,
    DropdownItem,
    FlexIcon,
    ButtonIcon,
    ToolbarButton,
    Popover,
  },

  props: {
    items: {
      type: Array,
    },
  },

  computed: {
    activeBlockName() {
      const block = this.blockItems.find(x => x.active);
      return block ? block.label.toString() : 'Block';
    },

    blockSelectorEnabled() {
      return this.blockItems.some(x => x.enabled);
    },

    blockItems() {
      return this.items.filter(x => x.group == 'blocks');
    },

    itemGroups() {
      return groupBy(
        this.items.filter(x => x.group != 'blocks'),
        x => x.group
      );
    },
  },

  methods: {
    itemClick(item) {
      if (item.enabled && item.execute) {
        item.execute();
      }
    },

    openChanged(item, visible) {
      if (!visible && item.enabled && item.reset) {
        item.reset();
      }
    },

    /**
     * Handle keydown event on a button
     *
     * @param {KeyboardEvent} e
     */
    handleButtonKeydown(e) {
      const tb = e.currentTarget;

      switch (e.key) {
        case 'ArrowLeft':
        case 'Left':
          this.$_moveFocus(tb, 'prev');
          break;

        case 'ArrowRight':
        case 'Right':
          this.$_moveFocus(tb, 'next');
          break;

        case 'Home':
          this.$_moveFocus(tb, 'first');
          break;

        case 'End':
          this.$_moveFocus(tb, 'last');
          break;

        default:
          return;
      }

      e.stopPropagation();
      e.preventDefault();
    },

    /**
     * Handle focus event on a button
     *
     * @param {FocusEvent} e
     */
    handleButtonFocus(e) {
      // update tabindex
      const currentButton = e.currentTarget;
      const buttons = this.$_getAllButtons();
      buttons.forEach(button => {
        button.tabIndex = button == currentButton ? 0 : -1;
      });
    },

    /**
     * Get all toolbar buttons in order
     *
     * @returns {Element[]}
     */
    $_getAllButtons() {
      return Array.prototype.slice.call(
        this.$el.querySelectorAll('.tui-editorWeka-toolbar__button')
      );
    },

    /**
     * Move focus from a button to a different button
     *
     * @param {Element} relativeTo
     * @param {('next'|'prev'|'first'|'last')} direction
     */
    $_moveFocus(relativeTo, direction) {
      const tabbable = getTabbableElements(this.$el);
      const buttons = this.$_getAllButtons().filter(x => tabbable.includes(x));

      const lastIndex = buttons.length - 1;

      let index = 0;
      if (direction == 'prev' || direction == 'next') {
        index = buttons.indexOf(relativeTo);
        if (index == -1) {
          index = direction == 'next' ? lastIndex : 0;
        } else {
          index += direction == 'next' ? 1 : -1;
          if (index > lastIndex) {
            index = 0;
          } else if (index < 0) {
            index = lastIndex;
          }
        }
      } else if (direction == 'first') {
        index = 0;
      } else if (direction == 'last') {
        index = lastIndex;
      }

      if (buttons[index]) {
        buttons[index].focus();
      }
    },
  },
};
</script>

<lang-strings>
{
  "editor_weka": ["format_as_blocktype_status", "label_toolbar"]
}
</lang-strings>
