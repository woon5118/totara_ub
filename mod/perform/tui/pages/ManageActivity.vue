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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @package mod_perform
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
          <h2>{{ pageHeading }}</h2>
        </GridItem>
      </Grid>

      <Tabs v-if="activity" :selected="initialTabId">
        <Tab
          v-for="({ component, name, id }, index) in tabs"
          :id="id"
          :key="index"
          :name="name"
          :always-render="true"
        >
          <component
            :is="component"
            v-model="activity"
            :activity-id="activityId"
            @mutation-error="showMutationErrorNotification"
            @mutation-success="showMutationSuccessNotification"
          />
        </Tab>
      </Tabs>
    </Loader>
  </div>
</template>

<script>
import AssignmentsTab from 'mod_perform/components/manage_activity/assignment/AssignmentsTab';
import ActivityContentTab from 'mod_perform/components/manage_activity/content/ActivityContentTab';
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import GeneralInfoTab from 'mod_perform/components/manage_activity/GeneralInfoTab';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Loader from 'totara_core/components/loader/Loader';
import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';
import activityQuery from 'mod_perform/graphql/activity.graphql';
import { notify } from 'totara_core/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';

export default {
  components: {
    AssignmentsTab,
    ActivityContentTab,
    FlexIcon,
    Tab,
    Tabs,
    Loader,
    GeneralInfoTab,
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
        // noinspection JSUnresolvedVariable
        return data.mod_perform_activity;
      },
    },
  },
  computed: {
    pageHeading() {
      // TODO switching based on status
      return this.$str(
        'manage_edit_draft_heading',
        'mod_perform',
        this.activity.name
      );
    },
  },
  methods: {
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
      "manage_activities_tabs_assignment",
      "manage_activities_tabs_content",
      "manage_activities_tabs_general",
      "back_to_all_activities",
      "manage_edit_draft_heading",
      "toast_error_generic_update",
      "toast_success_activity_update"
    ]
  }
</lang-strings>
