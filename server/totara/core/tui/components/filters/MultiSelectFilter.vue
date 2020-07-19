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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
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
