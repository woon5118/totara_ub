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
  <div class="tui-popoverFrame" :class="['tui-popoverFrame--' + side]">
    <Arrow :relative-side="side" :distance="arrowDistance" />
    <CloseButton class="tui-popoverFrame__close" @click="$emit('close')" />
    <div v-if="title" class="tui-popoverFrame__title">
      {{ title }}
    </div>
    <div class="tui-popoverFrame__content">
      <slot />
    </div>
    <div v-if="$scopedSlots.buttons" class="tui-popoverFrame__buttons">
      <slot name="buttons" />
    </div>
  </div>
</template>

<script>
import Arrow from 'totara_core/components/decor/Arrow';
import CloseButton from 'totara_core/components/buttons/CloseIcon';

export default {
  components: {
    Arrow,
    CloseButton,
  },

  props: {
    title: String,
    side: String,
    arrowDistance: Number,
  },
};
</script>
