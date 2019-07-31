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
  <div
    v-focus-within
    class="tui-inlineEditing"
    :class="{
      'tui-inlineEditing--updateAble': updateAble,
      'tui-inlineEditing--fullWidth': fullWidth,
    }"
    @click="handleClick"
  >
    <ButtonIcon
      v-show="updateAble"
      :aria-label="buttonAriaLabel"
      :styleclass="{ transparent: true, small: true }"
      class="tui-inlineEditing__btn"
      :class="{ 'tui-inlineEditing__btn--absoluteButton': absoluteButton }"
    >
      <EditIcon size="200" />
    </ButtonIcon>

    <slot name="content" />
  </div>
</template>

<script>
import EditIcon from 'tui/components/icons/common/Edit';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';

export default {
  components: {
    EditIcon,
    ButtonIcon,
  },

  props: {
    absoluteButton: Boolean,
    buttonAriaLabel: {
      type: String,
      default() {
        return this.$str('edit', 'moodle');
      },
    },
    fullWidth: Boolean,
    restrictedMode: Boolean,
    updateAble: {
      type: Boolean,
      required: true,
    },
  },

  methods: {
    handleClick(event) {
      if (!this.updateAble) {
        return;
      }

      if (this.restrictedMode) {
        // No event triggering when click inside elements of InlineEditing
        if (event.target !== event.currentTarget) {
          return;
        }
      }

      this.$emit('click');
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "edit"
    ]
  }
</lang-strings>
