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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_core
-->

<template>
  <Dropdown :close-on-click="false" :separator="separator">
    <template v-slot:trigger="{ toggle, isOpen }">
      <div class="tui-tagList" @click="handleClick(toggle, isOpen)">
        <div class="tui-tagList__tags">
          <OverflowDetector v-slot="{ measuring }" @change="overflowChanged">
            <div
              class="tui-tagList__tagItems"
              :class="{ 'tui-tagList__tagItems--open': isOpen }"
              aria-live="polite"
              role="status"
              aria-atomic="false"
              :aria-label="$str('tags_selected', 'totara_core')"
              aria-relevant="additions"
            >
              <template v-for="(tag, index) in tags">
                <slot
                  v-if="isOpen || measuring || index < visible"
                  name="tag"
                  :tag="tag"
                >
                  <Tag
                    :key="index"
                    :text="tag.text"
                    @mouseover.native="triggerTooltip"
                  >
                    <template v-slot:button>
                      <ButtonIcon
                        ref="tagIcon"
                        :disabled="disabled"
                        :styleclass="{
                          transparent: true,
                          primary: true,
                          small: true,
                        }"
                        :aria-label="
                          $str('tag_remove', 'totara_core') + ' ' + tag.text
                        "
                        @click.stop.prevent="handleRemove(tag, index)"
                      >
                        <Close size="100" />
                      </ButtonIcon>
                    </template>
                  </Tag>
                </slot>
              </template>
              <div class="tui-tagList__input">
                <InputText
                  v-show="isOpen"
                  :ref="inputRef"
                  v-model="itemName"
                  :styleclass="{ transparent: true }"
                  :disabled="disabled"
                  :aria-label="$str('tag_list', 'totara_core')"
                />
              </div>
            </div>
          </OverflowDetector>
          <span
            v-show="!isOpen && tags.length > visible"
            class="tui-tagList__suffix"
            >{{ $str('n_more', 'totara_core', tags.length - visible) }}</span
          >
        </div>
        <ButtonIcon
          ref="expandArrow"
          :aria-expanded="isOpen.toString()"
          :aria-label="
            $str(isOpen ? 'collapse' : 'expand', 'moodle') +
              ' ' +
              $str('tag_list', 'totara_core')
          "
          aria-haspopup="menu"
          :disabled="disabled"
          :styleclass="{ transparent: true }"
          @click.stop.prevent="expandList(toggle, isOpen)"
        >
          <Expand />
        </ButtonIcon>
      </div>
    </template>
    <template v-for="(item, index) in items">
      <DropdownItem :key="index" @click="dropdownItemClicked(item, index)">
        <slot name="item" :item="item" :index="index" />
      </DropdownItem>
    </template>
  </Dropdown>
</template>

<script>
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import InputText from 'tui/components/form/InputText';
import Expand from 'tui/components/icons/common/Show';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Close from 'tui/components/icons/common/Close';
import Tag from 'tui/components/tag/Tag';
import OverflowDetector from 'tui/components/util/OverflowDetector';

export default {
  components: {
    ButtonIcon,
    Dropdown,
    DropdownItem,
    InputText,
    Expand,
    Close,
    Tag,
    OverflowDetector,
  },
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
    tags: Array,
    items: Array,
    filter: String,
    separator: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      inputRef: 'tagListInput',
      clickedLabelName: '',
      itemName: this.filter || '',
      visible: Infinity,
    };
  },
  watch: {
    itemName() {
      this.$emit('filter', this.itemName);
    },
  },
  methods: {
    overflowChanged({ visible }) {
      this.visible = visible;
    },
    handleRemove(tag, index) {
      if (this.tags.length === 1) {
        this.focusInput();
      } else if (index == this.tags.length - 1) {
        this.$refs.tagIcon[index - 1].$el.focus();
      }

      this.$emit('remove', tag, index);
    },
    dropdownItemClicked(item, index) {
      this.$emit('select', item, index);
    },
    triggerTooltip() {
      console.log('hover should be added');
    },
    handleClick(toggle, isOpen) {
      // Open the dropdown when it's closed
      if (!isOpen) {
        // Reset the input value when opening the menu
        this.itemName = '';
        toggle();
      }

      this.focusInput();
    },
    expandList(toggle, isOpen) {
      toggle();

      // Focus on input after dropdown get opened
      if (!isOpen) {
        // Reset the input value when opening the menu
        this.itemName = '';
        this.focusInput();
      }
    },
    focusInput() {
      /**
       * 2 nextTick are required here as the focusInput should be triggered after the menu opened.
       * And detect the menu open status used one nextTicks already.
       */
      this.$nextTick(() => {
        this.$nextTick(() => {
          this.$refs[this.inputRef].$el.focus();
        });
      });
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "expand",
    "collapse"
  ],
  "totara_core": [
    "n_more",
    "tag_list",
    "tag_remove",
    "tags_selected"
  ]
}
</lang-strings>
