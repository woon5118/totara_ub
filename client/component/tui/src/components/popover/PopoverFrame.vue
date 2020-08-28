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
  @module tui
-->

<template>
  <div class="tui-popoverFrame" :class="['tui-popoverFrame--' + side]">
    <Arrow :relative-side="arrowSide" :distance="arrowDistance" />
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
import Arrow from 'tui/components/decor/Arrow';
import CloseButton from 'tui/components/buttons/CloseIcon';
import { langSide } from 'tui/i18n';

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

  computed: {
    arrowSide() {
      return langSide(this.side);
    },
  },
};
</script>

<style lang="scss">
.tui-popoverFrame {
  @include tui-font-body();
  position: relative;
  max-width: 300px;
  // margin must be equal on all 4 sides, and must not change with position
  margin: 10px;
  padding: var(--gap-4);
  background: var(--color-background);
  background-clip: padding-box;
  border: 1px solid var(--color-neutral-5);
  box-shadow: var(--shadow-3);

  &__close {
    position: absolute;
    top: 0;
    right: 0;
    display: flex;
    padding: calc(1.4rem - 0.1em) calc(1.4rem - 0.3em);
  }

  &__title {
    @include tui-font-heading-x-small();
    padding-top: var(--gap-2);
    padding-right: var(--gap-4);
  }

  &__content {
    padding: var(--gap-2) var(--gap-4) var(--gap-2) 0;
  }

  &__title + &__content {
    padding-right: 0;
  }

  &__buttons {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--gap-3);
    padding-bottom: var(--gap-3);

    > * + * {
      margin-left: var(--gap-4);
    }
  }
}
</style>
