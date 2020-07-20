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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performManageActivity">
    <a :href="goBackLink">{{
      $str('back_to_all_activities', 'mod_perform')
    }}</a>

    <Loader :loading="$apollo.loading">
      <Grid
        v-if="activity"
        :stack-at="768"
        class="tui-performManageActivity__topBar"
      >
        <GridItem :units="6">
          <h2 class="tui-performManageActivity__title">{{ activity.name }}</h2>
        </GridItem>
      </Grid>

      <ActivityStatusBanner
        v-if="activity"
        :activity="activity"
        @refetch="refetch"
      />

      <Tabs v-if="activity" :selected="initialTabId">
        <Tab
          v-for="({ component, name, id }, index) in tabs"
          :id="id"
          :key="index"
          :name="name"
        >
          <component
            :is="component"
            v-model="activity"
            :activity-id="activityId"
            :activity-state="activityState"
            @mutation-error="showMutationErrorNotification"
            @mutation-success="showMutationSuccessNotification"
          />
        </Tab>
      </Tabs>
    </Loader>
  </div>
</template>

<script>
import ActivityStatusBanner from 'mod_perform/components/manage_activity/ActivityStatusBanner';
import AssignmentsTab from 'mod_perform/components/manage_activity/assignment/AssignmentsTab';
import ActivityContentTab from 'mod_perform/components/manage_activity/content/ActivityContentTab';
import FlexIcon from 'tui/components/icons/FlexIcon';
import GeneralInfoTab from 'mod_perform/components/manage_activity/GeneralInfoTab';
import NotificationsTab from 'mod_perform/components/manage_activity/notification/NotificationsTab';
import Grid from 'tui/components/grid/Grid';
import GridItem from 'tui/components/grid/GridItem';
import Loader from 'tui/components/loader/Loader';
import Tab from 'tui/components/tabs/Tab';
import Tabs from 'tui/components/tabs/Tabs';
import activityQuery from 'mod_perform/graphql/activity';
import { notify } from 'tui/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';

export default {
  components: {
    ActivityStatusBanner,
    AssignmentsTab,
    ActivityContentTab,
    FlexIcon,
    Tab,
    Tabs,
    Loader,
    GeneralInfoTab,
    NotificationsTab,
    Grid,
    GridItem,
  },
  props: {
    activityId: {
      required: true,
      type: Number,
    },
    goBackLink: {
      required: true,
      type: String,
    },
  },
  data() {
    const generalInfoTabId = this.$id('genral-info-tab');
    const contentTabId = this.$id('content-tab');
    const assignmentTabId = this.$id('assignments-tab');
    const notificationTabId = this.$id('notifications-tab');

    return {
      activity: null,
      initialTabId: contentTabId,
      tabs: [
        {
          id: generalInfoTabId,
          component: 'GeneralInfoTab',
          name: this.$str('manage_activities_tabs_general', 'mod_perform'),
        },
        {
          id: contentTabId,
          component: 'ActivityContentTab',
          name: this.$str('manage_activities_tabs_content', 'mod_perform'),
        },
        {
          id: assignmentTabId,
          component: 'AssignmentsTab',
          name: this.$str('manage_activities_tabs_assignment', 'mod_perform'),
        },
        {
          id: notificationTabId,
          component: 'NotificationsTab',
          name: this.$str(
            'manage_activities_tabs_notifications',
            'mod_perform'
          ),
        },
      ],
    };
  },
  apollo: {
    activity: {
      query: activityQuery,
      variables() {
        return {
          activity_id: this.activityId,
        };
      },
      update: data => {
        return data.mod_perform_activity;
      },
    },
  },
  computed: {
    activityState() {
      return this.activity ? this.activity.state_details.name : null;
    },
  },
  methods: {
    /**
     * Re-fetch the activity from the server.
     */
    refetch() {
      this.$apollo.queries.activity.refetch();
    },

    /**
     * Show a generic success toast.
     */
    showMutationSuccessNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_success_activity_update', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Show a generic saving error toast.
     */
    showMutationErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "back_to_all_activities",
      "manage_activities_tabs_assignment",
      "manage_activities_tabs_content",
      "manage_activities_tabs_general",
      "manage_activities_tabs_notifications",
      "toast_error_generic_update",
      "toast_success_activity_update"
    ]
  }
</lang-strings>
