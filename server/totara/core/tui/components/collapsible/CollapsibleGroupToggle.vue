<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
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
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import CollapseIcon from 'totara_core/components/icons/common/Collapse';
import ExpandIcon from 'totara_core/components/icons/common/Expand';

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
