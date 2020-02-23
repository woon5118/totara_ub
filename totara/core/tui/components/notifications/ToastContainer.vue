<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
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
