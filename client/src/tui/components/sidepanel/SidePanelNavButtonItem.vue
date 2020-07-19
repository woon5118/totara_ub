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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <li
    v-focus-within
    class="tui-sidePanelNavButtonItem"
    :class="{ 'tui-sidePanelNavButtonItem--active': activeItem }"
  >
    <Button
      class="tui-sidePanelNavButtonItem__action"
      :aria-current="activeItem ? 'location' : null"
      :text="text"
      :styleclass="{
        transparent: true,
      }"
      @click="$emit('select', { action: action, id: id })"
    />

    <div v-if="$scopedSlots.default" class="tui-sidePanelNavButtonItem__side">
      <slot />
    </div>
  </li>
</template>

<script>
import Button from 'tui/components/buttons/Button';

export default {
  components: {
    Button,
  },

  props: {
    action: String,
    active: [Boolean, Number, String],
    id: {
      type: [Number, String],
      required: true,
    },
    text: String,
  },

  computed: {
    /**
     * Check if this is the active item
     *
     * @return {Bool}
     */
    activeItem() {
      if (this.active == this.id) {
        return true;
      }
      return false;
    },
  },
};
</script>
