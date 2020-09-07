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

  @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
  @module mod_perform
-->

<template>
  <Loader :loading="isLoading">
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
        @updateTriggers="onUpdateTriggers"
      />
    </div>
  </Loader>
</template>

<script>
import { notify } from 'tui/notifications';
import Loader from 'tui/components/loader/Loader';
import NotificationSection from 'mod_perform/components/manage_activity/notification/NotificationSection';
import notificationsQuery from 'mod_perform/graphql/notifications';
import createNotificationMutation from 'mod_perform/graphql/create_notification';
import toggleNotificationMutation from 'mod_perform/graphql/toggle_notification';
import toggleNotificationRecipientMutation from 'mod_perform/graphql/toggle_notification_recipient';
import updateNotificationTriggersMutation from 'mod_perform/graphql/update_notification_triggers';
import { debounce } from 'tui/util';

export default {
  components: {
    Loader,
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
      isLoading: false,
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

  beforeCreate() {
    this.updateTriggers = debounce(
      async (section, triggers) => {
        const id = await this.createNotificationIfNotExists(section);
        try {
          await this.$apollo.mutate({
            mutation: updateNotificationTriggersMutation,
            variables: {
              input: {
                notification_id: id,
                values: triggers,
              },
            },
          });
          notify({
            message: this.$str('toast_success_activity_update', 'mod_perform'),
            type: 'success',
          });
        } catch (ex) {
          notify({
            message: this.$str('toast_error_generic_update', 'mod_perform'),
            type: 'error',
          });
          throw ex;
        }
      },
      500,
      { leading: true, trailing: true }
    );
  },

  methods: {
    async onToggleNotification(section, active) {
      this.isLoading = true;
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
      await this.$apollo.queries.notifications.refetch();
      this.isLoading = false;
    },

    async createNotificationIfNotExists(section) {
      let id;
      if (!section.id) {
        this.isLoading = true;
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
        await this.$apollo.queries.notifications.refetch();
        this.isLoading = false;
      } else {
        id = section.id;
      }
      return id;
    },

    async onToggleRecipient(section, recipient) {
      this.isLoading = true;
      const id = await this.createNotificationIfNotExists(section);
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
      await this.$apollo.queries.notifications.refetch();
      this.isLoading = false;
    },

    onUpdateTriggers(section, triggers) {
      this.updateTriggers(section, triggers);
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "notification_active",
      "manage_activities_tabs_notifications",
      "toast_error_generic_update",
      "toast_success_activity_update"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-activityNotifications {
  &__header {
    display: flex;
    align-items: baseline;
    margin-top: var(--gap-2); // Check the content tab before changing this.
  }

  &__title {
    @include tui-font-heading-small();
    flex-basis: 100%;
  }

  &__active {
    @include tui-font-heading-label-small();
    margin-right: var(--gap-4);
  }
}
</style>
