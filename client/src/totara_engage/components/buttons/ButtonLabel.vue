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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module totara_engage
-->

<template>
  <div class="tui-totaraEngage-buttonLabel">
    <ButtonIcon
      :aria-label="ariaLabel"
      :disabled="disabled"
      :styleclass="styleclass"
      @click="$emit('click')"
    >
      <slot name="icon" />
    </ButtonIcon>

    <Popover
      v-if="$scopedSlots.hoverContent"
      :triggers="['focus', 'hover']"
      @open-changed="bubbleOpen"
    >
      <template v-slot:trigger>
        <a
          href="#"
          class="tui-totaraEngage-buttonLabel__label"
          @click="$emit('open')"
        >
          {{ number }}
        </a>
      </template>
      <slot name="hoverContent" />
    </Popover>
    <template v-else>
      <span class="tui-totaraEngage-buttonLabel__label" @click="$emit('open')">
        {{ number }}
      </span>
    </template>
  </div>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import Popover from 'tui/components/popover/Popover';

export default {
  components: {
    ButtonIcon,
    Popover,
  },

  props: {
    number: {
      type: [Number, String],
      required: true,
    },
    ariaLabel: {
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

  methods: {
    bubbleOpen(val) {
      this.$emit('popover-opened', val);
    },
  },
};
</script>
