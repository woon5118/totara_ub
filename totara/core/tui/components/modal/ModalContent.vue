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
  <div class="tui-modalContent">
    <div class="tui-modalContent__header">
      <div :id="titleId" class="tui-modalContent__header-title">
        {{ title || '' }}
        <slot name="title" />
      </div>

      <CloseButton
        v-if="closeButton"
        :class="'tui-modalContent__header-close'"
        :size="300"
        @click="dismiss()"
      />
    </div>
    <div class="tui-modalContent__content">
      <slot />
    </div>
    <div v-if="$scopedSlots.buttons" class="tui-modalContent__footer">
      <div class="tui-modalContent__footer-buttons">
        <ButtonGroup>
          <slot name="buttons" />
        </ButtonGroup>
      </div>
    </div>
  </div>
</template>

<script>
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import CloseButton from 'totara_core/components/buttons/CloseIcon';

export default {
  components: {
    ButtonGroup,
    CloseButton,
  },

  props: {
    title: String,
    titleId: String,
    closeButton: Boolean,
  },

  methods: {
    dismiss() {
      this.$emit('dismiss');
    },
  },
};
</script>
