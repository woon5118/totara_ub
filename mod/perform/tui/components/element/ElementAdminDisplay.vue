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
        <div class="tui-elementEditDisplay__title">{{ name }}</div>
        <div class="tui-elementEditDisplay__action">
          <ButtonIcon
            :aria-label="$str('section_delete_element', 'mod_perform')"
            @click="remove"
          >
            <DeleteIcon size="200" />
          </ButtonIcon>
        </div>
      </div>
      <div class="tui-elementEditDisplay__inner-content">
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import DeleteIcon from 'totara_core/components/icons/common/Delete';

export default {
  components: {
    ButtonIcon,
    DeleteIcon,
  },

  props: {
    name: String,
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
    "section_delete_element"
  ]
  }
</lang-strings>
