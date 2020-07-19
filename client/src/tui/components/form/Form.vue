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
  @module tui
-->

<template>
  <form
    class="tui-form"
    :class="[
      !vertical && 'tui-form--horizontal',
      vertical && 'tui-form--vertical',
      'tui-form--inputWidth-' + inputWidth,
    ]"
    @submit="$emit('submit', $event)"
  >
    <slot />
  </form>
</template>

<script>
export default {
  props: {
    vertical: Boolean,

    inputWidth: {
      type: String,
      validator: x => ['full', 'limited'].includes(x),
      default: 'limited',
    },
  },
};
</script>
