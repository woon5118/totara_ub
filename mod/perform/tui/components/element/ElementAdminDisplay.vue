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

  @author Simon Chester <simon.chester@totaralearning.com>
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-elementEditDisplay" role="button" tabindex="0" @click="edit">
    <div v-if="error">{{ error }}</div>
    <div class="tui-elementEditDisplay__inner">
      <div class="tui-elementEditDisplay__inner-header">
        <div class="tui-elementEditDisplay__title">{{ title }}</div>
        <div class="tui-elementEditDisplay__info">
          <Lozenge
            v-show="isRequired"
            :text="$str('section_element_tag_required', 'mod_perform')"
          />
          <Lozenge
            v-show="!isRequired"
            :text="$str('section_element_tag_optional', 'mod_perform')"
            type="warning"
          />
        </div>
      </div>
      <div class="tui-elementEditDisplay__inner-content">
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script>
import Lozenge from 'totara_core/components/lozenge/Lozenge';

export default {
  components: {
    Lozenge,
  },

  props: {
    title: String,
    isRequired: Boolean,
    type: Object,
    error: String,
  },
  methods: {
    edit() {
      this.$emit('edit');
    },
    remove() {
      this.$emit('remove');
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform": [
    "section_element_tag_required",
    "section_element_tag_optional"
  ]
  }
</lang-strings>
