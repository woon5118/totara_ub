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
  <div
    class="tui-searchFilter"
    :class="{ 'tui-searchFilter--stacked': stacked }"
  >
    <Label
      v-if="!dropLabel"
      :for-id="generatedId"
      :hidden="!showLabel"
      :label="label"
    />
    <div class="tui-searchFilter__group">
      <div class="tui-searchFilter__group-icon">
        <SearchIcon />
      </div>
      <InputSearch
        :id="generatedId"
        v-bind="$props"
        :aria-label="ariaLabel || label"
        :styleclass="{ preIcon: true }"
        @input="input"
        @submit="submit"
      />
    </div>
  </div>
</template>

<script>
// Components
import InputSearch from 'totara_core/components/form/InputSearch';
import Label from 'totara_core/components/form/Label';
import SearchIcon from 'totara_core/components/icons/common/Search';

export default {
  components: {
    InputSearch,
    Label,
    SearchIcon,
  },
  inheritAttrs: false,

  /* eslint-disable vue/require-prop-types */
  props: {
    ariaDescribedby: {},
    ariaLabel: {},
    ariaLabelledby: {},
    disabled: {},
    dropLabel: {
      required: false,
      type: Boolean,
    },
    id: {},
    label: {
      required: true,
      type: String,
    },
    list: {},
    maxlength: {},
    minlength: {},
    name: {},
    pattern: {},
    placeholder: {},
    showLabel: {
      required: false,
      type: Boolean,
    },
    size: {},
    spellcheck: {},
    stacked: {},
    value: {},
  },

  computed: {
    generatedId() {
      return this.id || this.$id();
    },
  },

  methods: {
    input(e) {
      this.$emit('input', e);
    },

    submit() {
      this.$emit('submit');
    },
  },
};
</script>
