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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <ModalPresenter :open="open" @request-close="$emit('cancel')">
    <Modal size="large" :aria-labelledby="id + 'title'">
      <ModalContent
        :close-button="true"
        :title="title"
        :title-id="id + 'title'"
        @dismiss="$emit('cancel')"
      >
        <div class="tui-adder">
          <div class="tui-adder__tabs">
            <Tabs :transparent-tabs="true">
              <!-- Browse tab -->
              <Tab id="browse" :name="$str('adder_browse', 'totara_core')">
                <!-- Browse Filters -->
                <div class="tui-adder__filters">
                  <slot name="browse-filters" />
                </div>

                <!-- Browse List -->
                <div class="tui-adder__list">
                  <slot
                    name="browse-list"
                    :disabled-items="existingItems"
                    :selected-items="selectedItems"
                    :update="selectionUpdate"
                  />
                </div>
              </Tab>

              <!-- Selection (basket) Tab -->
              <Tab id="basket" :name="basketTabString">
                <div class="tui-adder__list">
                  <slot
                    name="basket-list"
                    :selected-items="selectedItems"
                    :update="selectionUpdate"
                  />
                </div>
              </Tab>
            </Tabs>
          </div>

          <!-- Footer (count & action buttons) -->
          <div class="tui-adder__footer">
            <div class="tui-adder__summary">Items selected: {{ count }}</div>
            <div class="tui-adder__actions">
              <ButtonGroup>
                <Button
                  :disabled="!count"
                  :text="$str('add', 'totara_core')"
                  :styleclass="{ primary: true }"
                  @click="$emit('added', selectedItems)"
                />
                <ButtonCancel @click="$emit('cancel')" />
              </ButtonGroup>
            </div>
          </div>
        </div>
      </ModalContent>
    </Modal>
  </ModalPresenter>
</template>

<script>
// Components
import Button from 'totara_core/components/buttons/Button';
import ButtonCancel from 'totara_core/components/buttons/Cancel';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Modal from 'totara_core/components/modal/Modal';
import ModalContent from 'totara_core/components/modal/ModalContent';
import ModalPresenter from 'totara_core/components/modal/ModalPresenter';
import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';

export default {
  components: {
    Button,
    ButtonCancel,
    ButtonGroup,
    Modal,
    ModalContent,
    ModalPresenter,
    Tab,
    Tabs,
  },

  props: {
    closeButton: {
      type: Boolean,
      default: true,
    },
    existingItems: {
      type: Array,
      default: () => [],
    },
    open: {
      type: Boolean,
    },
    title: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      count: 0,
      selectedItems: [],
    };
  },

  computed: {
    id() {
      return this.$id();
    },

    /**
     * Update the basket tab string based on selected count
     *
     */
    basketTabString() {
      if (this.count) {
        return this.$str(
          'adder_selection_with_count',
          'totara_core',
          this.count
        );
      }
      return this.$str('adder_selection', 'totara_core');
    },
  },

  watch: {
    /**
     * On opening of adder
     * Reset count and include existing items in selection
     *
     */
    open() {
      if (this.open) {
        this.count = 0;
        this.selectedItems = this.existingItems;
      }
    },
  },

  methods: {
    /**
     * Selection has changed
     * Update count & selected items
     *
     * @param {Array} selection
     */
    selectionUpdate(selection) {
      this.count = selection.length - this.existingItems.length;
      this.selectedItems = selection;
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "add",
    "adder_browse",
    "adder_selection",
    "adder_selection_with_count"
  ]
}
</lang-strings>
