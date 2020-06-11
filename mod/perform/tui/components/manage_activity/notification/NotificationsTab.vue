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

  @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-activityNotifications">
    <h3 class="tui-activityNotifications__header">
      <span class="tui-activityNotifications__title">{{
        $str('manage_activities_tabs_notifications', 'mod_perform')
      }}</span>
      <span class="tui-activityNotifications__active" aria-hidden="true">{{
        $str('notification_active', 'mod_perform')
      }}</span>
    </h3>
    <NotificationSection
      v-for="notification in notifications"
      :key="notification.class_key"
      :data="notification"
      @toggleNotification="onToggleNotification"
      @toggleRecipient="onToggleRecipient"
    />
  </div>
</template>

<script>
import NotificationSection from 'mod_perform/components/manage_activity/notification/NotificationSection';
import notificationsQuery from 'mod_perform/graphql/notifications';
import createNotificationMutation from 'mod_perform/graphql/create_notification';
import toggleNotificationMutation from 'mod_perform/graphql/toggle_notification';
import toggleNotificationRecipientMutation from 'mod_perform/graphql/toggle_notification_recipient';

export default {
  components: {
    NotificationSection,
  },

  props: {
    value: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      notifications: [],
    };
  },

  apollo: {
    notifications: {
      query: notificationsQuery,
      variables() {
        return { activity_id: this.value.id };
      },
      update: data => data.mod_perform_notifications,
    },
  },

  watch: {
    value() {
      this.$apollo.queries.notifications.refetch();
    },
  },

  methods: {
    async onToggleNotification(section, active) {
      if (section.id) {
        await this.$apollo.mutate({
          mutation: toggleNotificationMutation,
          variables: {
            input: {
              notification_id: section.id,
              active,
            },
          },
        });
      } else {
        await this.$apollo.mutate({
          mutation: createNotificationMutation,
          variables: {
            input: {
              activity_id: this.value.id,
              class_key: section.class_key,
              active,
            },
          },
        });
      }
    },

    async onToggleRecipient(section, recipient) {
      let id;
      if (!section.id) {
        const mutationResult = await this.$apollo.mutate({
          mutation: createNotificationMutation,
          variables: {
            input: {
              activity_id: this.value.id,
              class_key: section.class_key,
              active: section.active,
            },
          },
        });
        id =
          mutationResult.data.mod_perform_create_notification.notification.id;
      } else {
        id = section.id;
      }
      await this.$apollo.mutate({
        mutation: toggleNotificationRecipientMutation,
        variables: {
          input: {
            notification_id: id,
            relationship_id: recipient.id,
            active: recipient.active,
          },
        },
      });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "notification_active",
      "manage_activities_tabs_notifications"
    ]
  }
</lang-strings>
