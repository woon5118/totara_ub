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
  <div class="tui-searchBox">
    <Label
      v-if="!dropLabel"
      :for-id="generatedId"
      :hidden="!labelVisible"
      :label="ariaLabel || placeholder"
    />
    <InputSearch
      :id="generatedId"
      v-bind="$props"
      :aria-label="ariaLabel || placeholder"
      :disabled="disabled"
      :placeholder="placeholder || $str('search', 'moodle')"
      @input="input"
      @submit="submit"
    />
    <ButtonIcon
      :aria-label="ariaLabel || placeholder"
      :disabled="disabled"
      :styleclass="{ small: true, transparent: true }"
      @click="submit"
    >
      <SearchIcon />
    </ButtonIcon>
  </div>
</template>

<script>
// Components
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import InputSearch from 'totara_core/components/form/InputSearch';
import Label from 'totara_core/components/form/Label';
import SearchIcon from 'totara_core/components/icons/common/Search';

export default {
  components: {
    ButtonIcon,
    InputSearch,
    Label,
    SearchIcon,
  },
  inheritAttrs: false,

  /* eslint-disable vue/require-prop-types */
  props: [
    'ariaDescribedby',
    'ariaLabel',
    'ariaLabelledby',
    'disabled',
    'dropLabel',
    'id',
    'labelVisible',
    'list',
    'maxlength',
    'minlength',
    'name',
    'pattern',
    'placeholder',
    'size',
    'spellcheck',
    'value',
  ],

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

<lang-strings>
{
  "moodle": [
    "search"
  ]
}
</lang-strings>
