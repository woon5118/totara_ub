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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module editor_weka
-->

<template>
  <ButtonIcon
    class="tui-editorWeka-toolbarButton"
    :styleclass="{ toolbar: true, selected: selected }"
    :aria-label="text"
    :aria-pressed="ariaPressed"
    :title="text"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <slot name="icon" />
  </ButtonIcon>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';

export default {
  components: {
    ButtonIcon,
  },

  props: {
    text: {
      type: String,
      required: true,
    },

    // null = not selectable
    selected: {
      type: Boolean,
      default: null,
    },

    disabled: Boolean,
  },

  computed: {
    ariaPressed() {
      if (this.selected == null) {
        return false; // unset - not pressable
      }
      return this.selected ? 'true' : 'false';
    },
  },
};
</script>
