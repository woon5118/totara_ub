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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <Fieldset class="tui-multiSelectFilter" :legend="title">
    <template v-for="{ label, id } in options">
      <div :key="id" class="tui-multiSelectFilter__item">
        <CheckboxButton
          :checked="isItemSelected(id)"
          @change="itemStateChange(id, $event)"
        >
          {{ label }}
        </CheckboxButton>
      </div>
    </template>
  </Fieldset>
</template>

<script>
import CheckboxButton from 'totara_core/components/form/CheckboxButton';
import Fieldset from 'totara_core/components/form/Fieldset';

export default {
  components: {
    CheckboxButton,
    Fieldset,
  },

  props: {
    options: Array,
    title: String,
    value: Array,
  },

  methods: {
    /**
     * remove selected option ID from selection and emit the update
     *
     * @param {Int} id
     * @param {Array} selection
     */
    deselectItem(id, selection) {
      if (selection.indexOf(id) !== -1) {
        selection.splice(selection.indexOf(id), 1);
      }
      this.$emit('input', selection);
    },

    /**
     * Check if item is selected
     *
     * @param {Int} id
     * @return {Boolean}
     */
    isItemSelected(id) {
      return this.value.indexOf(id) !== -1;
    },

    /**
     * item selection state has changed
     *
     * @param {Int} id
     * @param {Boolean} checked
     */
    itemStateChange(id, checked) {
      let selection = [].concat(this.value);
      if (!checked) {
        this.deselectItem(id, selection);
      } else {
        this.selectItem(id, selection);
      }
    },

    /**
     * Add selected option ID to selection and emit the update
     *
     * @param {Int} id
     * @param {Array} selection
     */
    selectItem(id, selection) {
      // If not already selected
      if (!this.value.includes(id)) {
        selection = selection.concat([id]);
        this.$emit('input', selection);
      }
    },
  },
};
</script>
