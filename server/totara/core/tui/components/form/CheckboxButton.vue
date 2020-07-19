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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-checkboxButton">
    <input
      :id="id"
      class="tui-checkboxButton__input"
      type="checkbox"
      :aria-label="ariaLabel"
      :aria-labelledby="ariaLabelledby"
      :autocomplete="autocomplete"
      :autofocus="autofocus"
      :checked="checked"
      :disabled="disabled"
      :name="name"
      :readonly="readonly"
      :required="required"
      :value="value"
      @change="handleChange"
    />
    <label class="tui-checkboxButton__label" :for="id">
      <slot />
      <CloseIcon custom-class="tui-checkboxButton__deselectIcon" />
    </label>
  </div>
</template>

<script>
import CloseIcon from 'totara_core/components/icons/common/Close';

export default {
  components: {
    CloseIcon,
  },

  model: {
    prop: 'checked',
    event: 'change',
  },

  props: {
    ariaLabel: String,
    ariaLabelledby: String,
    autocomplete: Boolean,
    autofocus: Boolean,
    checked: Boolean,
    disabled: Boolean,
    id: {
      type: String,
      default() {
        return this.uid;
      },
    },
    name: String,
    readonly: Boolean,
    required: Boolean,
    value: String,
  },

  methods: {
    handleChange(e) {
      this.$emit('change', e.target.checked);
    },
  },
};
</script>
