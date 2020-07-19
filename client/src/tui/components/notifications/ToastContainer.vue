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
    class="tui-toastContainer"
    :class="[hasItems && 'tui-toastContainer--hasItems']"
  >
    <div class="tui-toastContainer__inner" role="status" aria-live="polite">
      <transition-group name="tui-toastContainer__list-transition" tag="div">
        <NotificationBanner
          v-for="notification in activeNotifications"
          :key="notification.id"
          class="tui-toastContainer__list-item"
          :dismissable="true"
          :message="notification.message"
          :toast="true"
          :type="notification.type"
          @dismiss="dismiss(notification)"
        />
      </transition-group>
    </div>
  </div>
</template>

<script>
// Components
import NotificationBanner from 'totara_core/components/notifications/NotificationBanner';
// Util
import { pull, uniqueId } from 'totara_core/util';

export default {
  components: {
    NotificationBanner,
  },

  data() {
    return {
      activeNotifications: [],
    };
  },

  computed: {
    /**
     * Check if there are active notifications
     *
     * @return {Boolean}
     */
    hasItems() {
      return this.activeNotifications.length > 0;
    },
  },

  methods: {
    /**
     * Add notification to activeNotifications array
     * Called from totara_core/notifications
     *
     * @param {Object} options
     */
    addNotification(options) {
      options = Object.assign({}, options, { id: uniqueId() });
      this.activeNotifications.push(options);
      if (options.duration != null) {
        setTimeout(
          () => pull(this.activeNotifications, options),
          options.duration
        );
      }
    },

    /**
     * Remove notification from active array (and UI)
     *
     * @param {Object} notification
     */
    dismiss(notification) {
      pull(this.activeNotifications, notification);
    },
  },
};
</script>
