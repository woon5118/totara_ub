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
  <div
    class="tui-modalContent"
    :class="{ 'tui-modalContent--noContentPadding': !contentPadding }"
  >
    <div class="tui-modalContent__header">
      <div
        :id="titleId"
        class="tui-modalContent__header-title"
        :class="{ 'tui-modalContent__header-title--sronly': !titleVisible }"
      >
        {{ title || '' }}
        <slot name="title" />
      </div>

      <CloseButton
        v-if="closeButton"
        :class="'tui-modalContent__header-close'"
        :size="300"
        @click="dismiss()"
      />
    </div>
    <div class="tui-modalContent__content">
      <slot />
    </div>
    <div
      v-if="$scopedSlots['footer-content'] || $scopedSlots.buttons"
      class="tui-modalContent__footer"
    >
      <slot name="footer-content">
        <div class="tui-modalContent__footer-buttons">
          <ButtonGroup>
            <slot name="buttons" />
          </ButtonGroup>
        </div>
      </slot>
    </div>
  </div>
</template>

<script>
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import CloseButton from 'tui/components/buttons/CloseIcon';

export default {
  components: {
    ButtonGroup,
    CloseButton,
  },

  props: {
    title: {
      type: String,
      required: false,
      validator: x => !x || x.slice(0).trim().length > 0,
    },
    titleId: String,
    titleVisible: {
      type: Boolean,
      default: true,
    },
    closeButton: Boolean,
    contentPadding: {
      type: Boolean,
      default: true,
    },
  },

  mounted() {
    if (
      this.titleVisible &&
      !this.title &&
      !(this.$slots.title && this.$slots.title.length > 0)
    ) {
      console.error(
        '[ModalContent] You must pass either a non-empty title prop or define a non-empty title slot.'
      );
    }
  },

  methods: {
    dismiss() {
      this.$emit('dismiss');
    },
  },
};
</script>

<style lang="scss">
:root {
  --modal-content-separation: var(--gap-2);
  --modal-content-separation-large: var(--gap-8);
  --modal-content-outer-padding: var(--gap-8);
}

.tui-modalContent {
  @include tui-font-body();
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-height: 0;
  padding: var(--modal-content-outer-padding) 0;

  &--noContentPadding {
    padding-bottom: 0;
  }

  &__header {
    display: flex;
    flex-shrink: 0;
    padding: 0 var(--modal-content-outer-padding);

    &-title {
      @include tui-font-heading-small();
      flex-grow: 1;
      overflow: hidden;

      &--sronly {
        @include sr-only();
      }
    }

    &-close {
      position: absolute;
      top: 0;
      right: 0;
      padding: var(--gap-3);
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    min-height: 0;
    margin-top: var(--modal-content-separation);
    padding: 0 var(--modal-content-outer-padding) 2px;
    overflow-y: auto;
  }

  &--noContentPadding &__content {
    padding: 0;
  }

  &__footer {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    margin-top: var(--modal-content-separation-large);
    padding: 0 var(--modal-content-outer-padding);

    &-buttons {
      margin-left: auto;
    }
  }
}
</style>
