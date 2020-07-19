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
  <div
    class="tui-radioGroup"
    :class="{ 'tui-radioGroup--horizontal': horizontal }"
    role="radiogroup"
    :aria-labelledby="ariaLabelledby"
  >
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
    ariaLabelledby: String,
    disabled: Boolean,
    horizontal: Boolean,
    name: {
      type: String,
      default() {
        return this.uid;
      },
    },
    required: Boolean,
    value: [Array, Boolean, Number, String],
  },

  methods: {
    provide({ props }) {
      return {
        props: {
          name: this.name,
          checked: props.value == this.value,
          disabled: this.disabled,
          required: this.required,
        },
        listeners: {
          select: this.$_handleSelect,
        },
      };
    },

    $_handleSelect(value) {
      this.$emit('input', value);
    },
  },
};
</script>
