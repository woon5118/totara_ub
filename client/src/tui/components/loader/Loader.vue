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
  <div
    class="tui-loader"
    :class="{ 'tui-loader--active': loading, 'tui-loader--fullpage': fullpage }"
  >
    <div v-if="emptySlot && loading" class="tui-loader__empty" />
    <slot />
    <div v-if="loading" class="tui-loader__overlay" role="alert">
      <div class="tui-loader__overlay-positioner">
        <div class="tui-loader__display">
          <Spinner aria-hidden="true" />
          {{ $str('loading', 'moodle') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Spinner from 'totara_core/components/icons/common/Spinner';

export default {
  components: {
    Spinner,
  },

  props: {
    fullpage: Boolean,
    loading: Boolean,
  },

  computed: {
    emptySlot() {
      return !this.$slots.default;
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "loading"
    ]
  }
</lang-strings>
