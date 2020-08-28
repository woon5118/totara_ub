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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module totara_engage
-->

<template>
  <span
    class="tui-totaraEngage-icon"
    :class="iconCss"
    :tabindex="clickable ? 0 : -1"
    @click="$_handleClick($event)"
  >
    <FlexIcon :icon="icon" :size="iconSize" :title="title" />
  </span>
</template>

<script>
import FlexIcon from 'tui/components/icons/FlexIcon';

export default {
  components: {
    FlexIcon: FlexIcon,
  },

  props: {
    clickable: {
      type: Boolean,
      default: false,
    },

    icon: {
      required: true,
      type: String,
    },

    title: {
      type: String,
      default: '',
    },

    iconSize: {
      type: [String, Number],
    },
  },

  computed: {
    iconCss() {
      if (this.clickable) {
        return 'tui-totaraEngage-icon--clickable';
      }

      return '';
    },
  },

  methods: {
    /**
     *
     * @param {MouseEvent} event
     */
    $_handleClick(event) {
      if (!this.clickable) {
        return;
      }

      this.$emit('click', event);
    },
  },
};
</script>

<style lang="scss">
.tui-totaraEngage-icon {
  display: flex;
  align-items: center;
  outline: none;
  cursor: default;
  &--clickable {
    cursor: pointer;
  }
}
</style>
