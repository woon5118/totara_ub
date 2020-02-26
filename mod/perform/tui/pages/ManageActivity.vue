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
    <!-- TODO use alert component when created -->
    <div
      v-if="mutationError"
      class="alert alert-danger alert-with-icon alert-dismissable fade-in"
      role="alert"
    >
      <button type="button" class="close" data-dismiss="alert">
        <FlexIcon icon="delete-ns" />
      </button>
      <div class="alert-icon">
        <FlexIcon icon="notification-error" />
      </div>
      <div class="alert-message">
        {{ $str('error_generic_mutation', 'mod_perform') }}
      </div>
    </div>

    <a :href="goBackLink">{{
      $str('perform:back_to_all_activities', 'mod_perform')
    }}</a>

    <Loader :loading="$apollo.loading">
      <Grid v-if="activity" :stack-at="768" class="tui-performManageActivity__top-bar">
        <GridItem :units="6">
          <h2>{{ pageHeading }}</h2>
        </GridItem>
      </Grid>

      <Tabs v-if="activity">
        <Tab
          v-for="({ component, name }, index) in tabs"
          :id="index"
          :key="index"
          :name="name"
          :always-render="true"
        >
          <component
            :is="component"
            v-model="activity"
            @mutation-error="mutationError = $event"
            @mutation-success="mutationError = null"
          />
        </Tab>
      </Tabs>
    </Loader>
  </div>
</template>

<script>
import FlexIcon from 'totara_core/components/icons/FlexIcon';
import GeneralInfoForm from 'mod_perform/components/manage_activity/GeneralInfoForm';
import ContentForm from 'mod_perform/components/manage_activity/ContentForm';
import Grid from 'totara_core/components/grid/Grid';
import GridItem from 'totara_core/components/grid/GridItem';
import Loader from 'totara_core/components/loader/Loader';
import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';
import activityQuery from 'mod_perform/graphql/activity.graphql';

export default {
  components: {
    ContentForm,
    FlexIcon,
    Tab,
    Tabs,
    Loader,
    GeneralInfoForm,
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
    return {
      activity: null,
      mutationError: null,
      tabs: [
        {
          component: 'GeneralInfoForm',
          name: this.$str('manage_activities_tabs:general', 'mod_perform'),
        },
        {
          component: 'ContentForm',
          name: this.$str('manage_activities_tabs:content', 'mod_perform'),
        },
        {
          component: 'Loader',
          name: this.$str('manage_activities_tabs:assignment', 'mod_perform'),
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
        'perform:manage_edit_draft_heading',
        'mod_perform',
        this.activity.name
      );
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "manage_activities_tabs:assignment",
      "manage_activities_tabs:content",
      "manage_activities_tabs:general",
      "perform:back_to_all_activities",
      "perform:manage_edit_draft_heading"
    ]
  }
</lang-strings>
