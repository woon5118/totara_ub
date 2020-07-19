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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-labelledButtonTrigger">
    <ButtonIcon
      :aria-label="buttonAriaLabel"
      :disabled="disabled"
      :styleclass="styleclass"
      @click="$emit('click', $event)"
    >
      <slot name="icon" />
    </ButtonIcon>

    <Popover
      v-if="$scopedSlots['hover-label-content']"
      :triggers="['focus', 'hover']"
      @open-changed="$emit('popover-open-changed', $event)"
    >
      <template v-slot:trigger="{ isOpen }">
        <Button
          :aria-expanded="isOpen ? 'true' : 'false'"
          :text="String(labelText)"
          :styleclass="{ transparent: true, small: true }"
          @click="$emit('open', $event)"
        />
      </template>
      <slot name="hover-label-content" />
    </Popover>

    <template v-else>
      <span
        class="tui-labelledButtonTrigger__label"
        @click="$emit('open', $event)"
      >
        {{ labelText }}
      </span>
    </template>
  </div>
</template>

<script>
import ButtonIcon from 'totara_core/components/buttons/ButtonIcon';
import Popover from 'totara_core/components/popover/Popover';
import Button from 'totara_core/components/buttons/Button';

export default {
  components: {
    ButtonIcon,
    Popover,
    Button,
  },

  props: {
    labelText: {
      type: [String, Number],
      required: true,
    },
    buttonAriaLabel: {
      type: String,
      required: true,
    },
    styleclass: {
      type: Object,
      default() {
        return {
          circle: true,
        };
      },
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
};
</script>
