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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <div class="tui-collapsibleGroupToggle">
    <ButtonIcon
      :aria-expanded="allExpanded.toString()"
      :aria-label="$str(allExpanded ? 'collapseall' : 'expandall', 'moodle')"
      class="tui-collapsibleGroupToggle__button"
      :styleclass="{
        transparent: true,
      }"
      :text="$str(allExpanded ? 'collapseall' : 'expandall', 'moodle')"
      @click="toggleAllFilters()"
    >
      <CollapseIcon v-if="allExpanded" size="200" />
      <ExpandIcon v-else size="200" />
    </ButtonIcon>
  </div>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import CollapseIcon from 'tui/components/icons/common/Collapse';
import ExpandIcon from 'tui/components/icons/common/Expand';

export default {
  components: {
    ButtonIcon,
    CollapseIcon,
    ExpandIcon,
  },

  props: {
    id: {
      type: [String, Number],
    },
    value: {
      required: true,
      type: Object,
    },
  },

  computed: {
    /**
     * Update expand state base on value
     *
     * @return {Bool}
     */
    allExpanded() {
      if (Object.values(this.value).findIndex(elem => elem === false) >= 0) {
        return false;
      }
      return true;
    },

    /**
     * Provide ID for accessibility tags
     *
     * @return {Bool}
     */
    generatedId() {
      return this.id || this.$id();
    },
  },

  methods: {
    /**
     * Emit updated object
     *
     */
    toggleAllFilters() {
      const newState = !this.allExpanded;
      let stateObj = this.value;

      Object.keys(stateObj).forEach(nestedKey => {
        stateObj[nestedKey] = newState;
      });
      this.$emit('input', stateObj);
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "expandall",
    "collapseall"
  ]
}
</lang-strings>
