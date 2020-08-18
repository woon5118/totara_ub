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
    <a :href="goBackLink">
      {{ $str('back_to_all_activities', 'mod_perform') }}
    </a>

    <Loader
      :loading="$apollo.loading"
      class="tui-performManageActivity__content"
    >
      <h2 v-if="activity" class="tui-performManageActivity__title">
        {{ activity.name }}
      </h2>

      <ActivityStatusBanner
        v-if="activity"
        :activity="activity"
        :disabled="activateModalLoading"
        @activate="activateActivity"
      />

      <Tabs
        v-if="activity"
        :selected="currentTabId"
        :controlled="true"
        @input="changeTabRequest"
      >
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
            :activity-state="activityState"
            :activity-context-id="parseInt(activity.context_id)"
            :activity-has-unsaved-changes="unsavedChanges"
            @unsaved-changes="setUnsavedChanges"
            @mutation-error="showMutationErrorNotification"
            @mutation-success="showMutationSuccessNotification"
          />
        </Tab>
      </Tabs>

      <ActivateActivityModal
        v-if="activity"
        :activity="activity"
        :trigger-open="showActivateModal"
        @close-activate-modal="updateShowActivateModal"
        @update-loading="uploadLoading"
        @unsaved-changes="setUnsavedChanges"
        @refetch="refetch"
      />
    </Loader>
  </div>
</template>

<script>
import ActivateActivityModal from 'mod_perform/components/manage_activity/ActivateActivityModal';
import ActivityContentTab from 'mod_perform/components/manage_activity/content/ActivityContentTab';
import ActivityStatusBanner from 'mod_perform/components/manage_activity/ActivityStatusBanner';
import AssignmentsTab from 'mod_perform/components/manage_activity/assignment/AssignmentsTab';
import FlexIcon from 'tui/components/icons/FlexIcon';
import GeneralInfoTab from 'mod_perform/components/manage_activity/GeneralInfoTab';
import NotificationsTab from 'mod_perform/components/manage_activity/notification/NotificationsTab';
import Loader from 'tui/components/loader/Loader';
import Tab from 'tui/components/tabs/Tab';
import Tabs from 'tui/components/tabs/Tabs';
import { notify } from 'tui/notifications';
import { debounce } from 'tui/util';

// graphQL
import activityQuery from 'mod_perform/graphql/activity';

export default {
  components: {
    ActivateActivityModal,
    ActivityContentTab,
    ActivityStatusBanner,
    AssignmentsTab,
    FlexIcon,
    Tab,
    Tabs,
    Loader,
    GeneralInfoTab,
    NotificationsTab,
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
      currentTabId: this.$id('content-tab'),
      tabs: [
        {
          id: this.$id('genral-info-tab'),
          component: 'GeneralInfoTab',
          name: this.$str('manage_activities_tabs_general', 'mod_perform'),
        },
        {
          id: this.$id('content-tab'),
          component: 'ActivityContentTab',
          name: this.$str('manage_activities_tabs_content', 'mod_perform'),
        },
        {
          id: this.$id('assignments-tab'),
          component: 'AssignmentsTab',
          name: this.$str('manage_activities_tabs_assignment', 'mod_perform'),
        },
        {
          id: this.$id('notifications-tab'),
          component: 'NotificationsTab',
          name: this.$str(
            'manage_activities_tabs_notifications',
            'mod_perform'
          ),
        },
      ],
      unsavedChanges: false,
      showActivateModal: false,
      activateModalLoading: false,
    };
  },
  computed: {
    activityState() {
      return this.activity ? this.activity.state_details.name : null;
    },
  },

  created() {
    this.showMutationSuccessNotification = debounce(
      this.showMutationSuccessNotification,
      500
    );
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
  methods: {
    /**
     * Change tab request has been made
     *
     * @param {String} id
     */
    changeTabRequest(id) {
      if (this.unsavedChanges) {
        const message = this.$str('unsaved_changes_warning', 'mod_perform');
        let answer = window.confirm(message);
        if (answer) {
          this.currentTabId = id;
          this.unsavedChanges = false;
        } else {
          return;
        }
      } else {
        this.currentTabId = id;
      }
    },

    /**
     * Re-fetch the activity from the server.
     */
    refetch() {
      this.$apollo.queries.activity.refetch();
    },

    /**
     * Show a generic saving error toast.
     */
    showMutationErrorNotification() {
      notify({
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Show a generic success toast.
     */
    showMutationSuccessNotification() {
      notify({
        message: this.$str('toast_success_activity_update', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Set if there is unsaved changes or not
     */
    setUnsavedChanges(hasUnsavedChanges) {
      this.unsavedChanges = hasUnsavedChanges;
    },

    /**
     * Check unsaved changes when click activate button from manage activity
     */
    activateActivity() {
      if (this.unsavedChanges) {
        const message = this.$str('unsaved_changes_warning', 'mod_perform');
        let answer = window.confirm(message);
        if (answer) {
          this.showActivateModal = true;
        } else {
          return;
        }
      } else {
        this.showActivateModal = true;
      }
    },

    updateShowActivateModal(value) {
      this.showActivateModal = value;
    },

    uploadLoading(value) {
      this.activateModalLoading = value;
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
