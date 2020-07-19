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
  <div class="tui-toggleSet">
    <PropsProvider :provide="provide">
      <slot />
    </PropsProvider>
  </div>
</template>

<script>
import PropsProvider from 'totara_core/components/util/PropsProvider';

export default {
  components: {
    PropsProvider,
  },

  props: {
    disabled: Boolean,
    value: String,
  },

  methods: {
    /**
     * Provide disabled & selected props to inner toggle buttons
     *
     * @param {string} selected
     */
    provide({ props }) {
      return {
        props: {
          disabled: this.disabled,
          selected: props.value == this.value,
        },
        listeners: {
          clicked: this.$_handleSelect,
        },
      };
    },

    /**
     * Toggle selected button
     *
     * @param {string} selected
     */
    $_handleSelect(selected) {
      this.$emit('input', selected);
    },
  },
};
</script>
