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
    v-if="!dismissed"
    class="tui-notificationBanner"
    :class="{
      ['tui-notificationBanner--' + type]: type,
      'tui-notificationBanner--toast': toast,
    }"
  >
    <div class="tui-notificationBanner__icon">
      <component
        :is="iconForType"
        :alt="labelForIconType + ': '"
        :title="labelForIconType"
        :size="200"
      />
    </div>
    <div class="tui-notificationBanner__message" v-html="message" />
    <div
      v-if="isDismissable"
      class="tui-notificationBanner__dismiss"
      aria-hidden="true"
    >
      <CloseButton
        class="tui-notificationBanner__dismiss_button"
        @click="dismiss"
      />
    </div>
  </div>
</template>

<script>
// Components
import CloseButton from 'tui/components/buttons/CloseIcon';
import ErrorIcon from 'tui/components/icons/common/Error';
import InfoIcon from 'tui/components/icons/common/Info';
import SuccessIcon from 'tui/components/icons/common/Success';
import WarningIcon from 'tui/components/icons/common/Warning';

const icons = {
  error: ErrorIcon,
  info: InfoIcon,
  success: SuccessIcon,
  warning: WarningIcon,
};

export default {
  components: {
    CloseButton,
  },

  props: {
    dismissable: Boolean,
    message: String,
    selfDismiss: Boolean,
    toast: {
      type: Boolean,
    },
    type: {
      type: String,
      default: 'info',
      validator: val => ['info', 'success', 'warning', 'error'].includes(val),
    },
  },

  data() {
    return {
      dismissed: false,
    };
  },

  computed: {
    /**
     * Check if the notification can be manualy closed
     *
     * @returns {boolean}
     */
    isDismissable() {
      return this.dismissable || this.selfDismiss;
    },

    /**
     * Return icon component for the type of notification
     *
     * @returns {Component}
     */
    iconForType() {
      return icons[this.type];
    },

    /**
     * Text to display for type icon.
     *
     * @returns {string}
     */
    labelForIconType() {
      switch (this.type) {
        case 'info':
        case 'success':
        case 'warning':
        case 'error':
          return this.$str(this.type);
        default:
          return null;
      }
    },
  },

  methods: {
    /**
     * Dismiss the notification
     *
     */
    dismiss() {
      if (this.dismissable) {
        this.$emit('dismiss');
      }
      if (this.selfDismiss) {
        this.dismissed = true;
      }
    },
  },
};
</script>

<lang-strings>
{
  "moodle": ["info", "success", "warning", "error"]
}
</lang-strings>
