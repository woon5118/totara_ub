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
  <div class="tui-toastContainer">
    <div class="tui-toastContainer__inner" role="status" aria-live="polite">
      <transition name="tui-toastContainer__item-transition" tag="div">
        <NotificationToast
          v-if="activeNotification"
          :message="activeNotification.message"
          :type="activeNotification.type"
          @dismiss="dismiss()"
        />
      </transition>
    </div>
  </div>
</template>

<script>
// Components
import NotificationToast from 'tui/components/notifications/NotificationToast';
// Util
import { uniqueId } from 'tui/util';

export default {
  components: {
    NotificationToast,
  },

  data() {
    return {
      activeNotification: null,
    };
  },

  methods: {
    /**
     * Add notification to activeNotification
     * Called from totara_core/notifications
     *
     * @param {Object} options
     */
    addNotification(options) {
      options = Object.assign({}, options, { id: uniqueId() });
      this.clearTimeout();
      this.activeNotification = options;

      if (options.duration != null) {
        this.timeout = setTimeout(
          () => (this.activeNotification = null),
          options.duration
        );
      }
    },

    /**
     * Clear previous timer if exists
     */
    clearTimeout() {
      if (this.timeout) {
        clearTimeout(this.timeout);
      }
    },

    /**
     * Remove notification from active object (and UI)
     *
     * @param {Object} notification
     */
    dismiss() {
      this.activeNotification = null;
    },
  },
};
</script>
