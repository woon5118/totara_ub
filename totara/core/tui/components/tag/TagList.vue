<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_core
-->

<template>
  <Dropdown :close-on-click="false" :separator="separator" aria-label="TagList">
    <template v-slot:trigger="{ toggle, isOpen }">
      <div class="tui-tagList" @click="handleClick(toggle, isOpen)">
        <div class="tui-tagList__tags">
          <OverflowDetector v-slot="{ measuring }" @change="overflowChanged">
            <div
              class="tui-tagList__tagItems"
              :class="{ 'tui-tagList__tagItems--open': isOpen }"
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
                        aria-label="removeTag"
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
          :aria-label="$str(isOpen ? 'collapse' : 'expand', 'moodle')"
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
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import InputText from 'totara_core/components/form/InputText';
import Expand from 'totara_core/components/icons/common/Show';
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Close from 'totara_core/components/icons/common/Close';
import Tag from 'totara_core/components/tag/Tag';
import OverflowDetector from 'totara_core/components/util/OverflowDetector';

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
      this.$refs['expandArrow'].$el.focus();
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
    "n_more"
  ]
}
</lang-strings>
