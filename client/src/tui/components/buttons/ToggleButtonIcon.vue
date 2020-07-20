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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <ButtonIcon
    :styleclass="{
      small: true,
      toggle: true,
      selected: selected,
    }"
    :aria-label="label"
    :aria-pressed="selected"
    :disabled="disabled"
    @click="toggleState"
  >
    <slot />
  </ButtonIcon>
</template>

<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';

export default {
  components: {
    ButtonIcon,
  },

  props: {
    disabled: Boolean,
    label: String,
    selected: Boolean,
    value: String,
  },

  methods: {
    /**
     * Emit button click event
     */
    toggleState() {
      // If already selected, don't emit clicked event
      if (this.selected) {
        return;
      }
      this.$emit('clicked', this.value);
    },
  },
};
</script>
