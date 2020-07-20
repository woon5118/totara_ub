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
  @module totara_core
-->

<template>
  <div>
    <div
      ref="backdrop"
      class="tui-modalBackdrop tui-modalBackdrop--animated"
      :class="{
        'tui-modalBackdrop--shade': shade,
        'tui-modalBackdrop--in': modalIn,
        ['tui-modalBackdrop--size-' + size]: true,
      }"
    />
    <div
      ref="modal"
      role="dialog"
      :aria-labelledby="ariaLabelledby"
      :aria-label="ariaLabel"
      class="tui-modal tui-modal--animated"
      :class="{
        'tui-modal--in': modalIn,
        'tui-modal--always-scroll': forceScroll,
        ['tui-modal--size-' + size]: true,
      }"
      tabindex="-1"
      @click="handleModalOuterClick"
    >
      <CloseButton
        v-if="dismissableSources.overlayClose"
        :aria-label="$str('closebuttontitle', 'core')"
        :class="'tui-modal__outsideClose'"
        :size="300"
        @click="dismiss()"
      />

      <div class="tui-modal__pad">
        <div ref="inner" class="tui-modal__inner">
          <CloseButton
            v-if="dismissableSources.overlayClose"
            :aria-label="$str('closebuttontitle', 'core')"
            :class="'tui-modal__close'"
            :size="300"
            @click="dismiss()"
          />
          <PropsProvider :provide="provideSlot">
            <slot />
          </PropsProvider>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Vue from 'vue';
import { waitForTransitionEnd } from 'tui/dom/transitions';
import { trapFocusOnTab } from 'tui/dom/focus';
import { bodySetModalOpen } from '../../js/internal/body_modal';
import { presenterInterfaceName } from 'tui/components/modal/ModalPresenter';
import PropsProvider from 'tui/components/util/PropsProvider';
import CloseButton from 'tui/components/buttons/CloseIcon';

export default {
  components: {
    CloseButton,
    PropsProvider,
  },

  props: {
    open: Boolean,
    size: {
      type: String,
      default: 'normal',
      validator(value) {
        return ['small', 'normal', 'large', 'sheet'].indexOf(value) !== -1;
      },
    },
    dismissable: {
      type: [Boolean, Object],
      default: true,
    },
    // tint the backdrop?
    shade: {
      type: Boolean,
      default: true,
    },
    ariaLabelledby: String,
    ariaLabel: String,
  },

  inject: {
    [presenterInterfaceName]: { default: null },
  },

  provide() {
    // hide presenter interface to avoid things inside modal
    // (including other modals) accidentally accessing it
    return { [presenterInterfaceName]: null };
  },

  data() {
    return {
      isOpen: false,
      closing: false,
      forceScroll: false,
      modalIn: false,
    };
  },

  computed: {
    presenterOpen() {
      return (
        this[presenterInterfaceName] && this[presenterInterfaceName].data.open
      );
    },

    shouldBeOpen() {
      return this.open || this.presenterOpen;
    },

    dismissableSources() {
      const defaultDismissable = {
        overlayClose: this.size == 'sheet',
        esc: true,
        backdropClick: true,
      };
      if (this.dismissable === true) {
        return defaultDismissable;
      } else if (this.dismissable === false) {
        return {};
      } else {
        return Object.assign(defaultDismissable, this.dismissable);
      }
    },
  },

  watch: {
    shouldBeOpen(open) {
      if (open) {
        this.$_open();
      } else {
        this.$_close();
      }
    },

    isOpen(isOpen) {
      if (this[presenterInterfaceName]) {
        this[presenterInterfaceName].setIsOpen(isOpen);
      }
    },
  },

  mounted() {
    this.$el.remove();
    this.$_removeElements();
    if (this.shouldBeOpen) {
      this.$_open();
    }
  },

  beforeDestroy() {
    this.$_removeElements();
    document.removeEventListener('keydown', this.$_handleDocumentKeydown);
    if (this.$_bodyModalHandle) {
      this.$_bodyModalHandle.close();
    }
  },

  methods: {
    dismiss() {
      this.requestClose();
    },

    requestClose(result) {
      this.$_emitRequestClose(result);
    },

    $_open() {
      if (this.isOpen) {
        return;
      }
      this.isOpen = true;
      this.$_bodyModalHandle = bodySetModalOpen();
      this.forceScroll = this.$_bodyModalHandle.scroll;

      document.addEventListener('keydown', this.$_handleDocumentKeydown);

      // accessibility: save previously focused element to restore when the dialog is closed
      this.previousFocus = document.activeElement;

      // accessibility: focus first focusable element within modal
      Vue.nextTick(() => {
        this.$refs.modal.focus();
      });

      this.$_animateOpen().then(() => {
        this.opening = false;
        if (!this.isOpen) {
          // handle modal being closed before it loads
          this.$_close();
        }
      });
    },

    $_animateOpen() {
      return new Promise(resolve => {
        document.body.appendChild(this.$refs.backdrop);
        document.body.appendChild(this.$refs.modal);

        // force reflow
        this.$refs.modal.offsetHeight;

        this.modalIn = true;
        resolve();
      });
    },

    $_close() {
      if (!this.closing) {
        this.closing = true;
        this.$_animateClose().then(() => {
          if (this.$_bodyModalHandle) {
            this.$_bodyModalHandle.close();
          }
          this.closing = false;
          this.isOpen = false;

          document.removeEventListener('keydown', this.$_handleDocumentKeydown);

          // accessibility: restore focus to previous element when modal closed
          if (this.previousFocus) {
            this.previousFocus.focus();
          }

          // handle case where open is toggled off and back on rapidly
          if (this.shouldBeOpen) {
            this.$_open();
          }
        });
      }
    },

    async $_animateClose() {
      this.modalIn = false;

      const transitionEls = [
        this.$refs.modal,
        this.$refs.inner,
        this.$refs.backdrop,
      ].filter(Boolean);

      await waitForTransitionEnd(transitionEls);

      this.$_removeElements();
    },

    $_emitRequestClose(result) {
      let ok = true;

      this.$emit('close', {
        result,
        cancel() {
          ok = false;
        },
      });

      if (this[presenterInterfaceName] && ok) {
        this[presenterInterfaceName].requestClose(result);
      }
    },

    $_handleDocumentKeydown(e) {
      switch (e.key) {
        case 'Escape':
          if (this.dismissableSources.esc) {
            this.dismiss();
          }
          break;
        case 'Tab':
          trapFocusOnTab(this.$refs.modal, e);
          break;
      }
    },

    handleModalOuterClick(e) {
      if (this.$refs.inner && !this.$refs.inner.contains(e.target)) {
        e.preventDefault();
        if (this.dismissableSources.backdropClick) {
          this.dismiss();
        }
      }
    },

    provideSlot() {
      return {
        listeners: {
          dismiss: this.dismiss,
        },
      };
    },

    $_removeElements() {
      this.$refs.backdrop.remove();
      this.$refs.modal.remove();
    },
  },
};
</script>

<lang-strings>
{
  "core": ["closebuttontitle"]
}
</lang-strings>
