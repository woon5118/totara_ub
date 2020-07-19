<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <div class="tui-modalContent">
    <div class="tui-modalContent__header">
      <div
        :id="titleId"
        class="tui-modalContent__header-title"
        :class="{ 'tui-modalContent__header-title--sronly': !titleVisible }"
      >
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
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import CloseButton from 'tui/components/buttons/CloseIcon';

export default {
  components: {
    ButtonGroup,
    CloseButton,
  },

  props: {
    title: {
      type: String,
      required: true,
    },
    titleId: String,
    titleVisible: {
      type: Boolean,
      default: true,
    },
    closeButton: Boolean,
  },

  methods: {
    dismiss() {
      this.$emit('dismiss');
    },
  },
};
</script>
