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
  <div class="tui-performUserActivities">
    <div class="tui-performUserActivities__header-row">
      <h2 class="tui-performUserActivities__heading">
        {{ $str('user_activities_page_title', 'mod_perform') }}
      </h2>

      <Button
        v-if="canPotentiallyManageParticipants"
        class="tui-performUserActivities__action-button"
        :text="$str('manage_participation', 'mod_perform')"
        @click="openParticipationModal = true"
      />
    </div>

    <ManualParticipantsSelectionBanner
      v-if="requireManualParticipantsNotification"
    />

    <div class="tui-performUserActivities__content">
      <Tabs :selected="initialTab">
        <Tab
          :id="$id('your-activities-tab')"
          :name="$str('user_activities_your_activities_title', 'mod_perform')"
        >
          <UserActivityList
            about="self"
            :current-user-id="currentUserId"
            :view-url="viewActivityUrl"
          />
        </Tab>
        <Tab
          :id="$id('activities-about-others-tab')"
          :name="
            $str('user_activities_activities_about_others_title', 'mod_perform')
          "
        >
          <UserActivityList
            about="others"
            :current-user-id="currentUserId"
            :view-url="viewActivityUrl"
          />
        </Tab>
      </Tabs>
    </div>

    <!--
      The v-if is important here because the query performed by the SelectActivityModal can be quite expensive
      so we only want to render/perform the component if we really need to
    -->
    <SelectActivityModal
      v-if="openParticipationModal"
      v-model="openParticipationModal"
    />
  </div>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import ManualParticipantsSelectionBanner from 'mod_perform/components/user_activities/ManualParticipantsSelectionBanner';
import SelectActivityModal from 'mod_perform/components/manage_activity/participation/SelectActivityModal';
import Tab from 'tui/components/tabs/Tab';
import Tabs from 'tui/components/tabs/Tabs';
import UserActivityList from 'mod_perform/components/user_activities/list/Activities';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
import { notify } from 'tui/notifications';

export default {
  components: {
    Button,
    ManualParticipantsSelectionBanner,
    SelectActivityModal,
    Tab,
    Tabs,
    UserActivityList,
  },
  props: {
    /**
     * The id of the logged in user.
     */
    currentUserId: {
      required: true,
      type: Number,
    },
    viewActivityUrl: {
      required: true,
      type: String,
    },
    showAboutOthersTab: {
      required: true,
      type: Boolean,
    },
    completionSaveSuccess: {
      required: true,
      type: Boolean,
    },
    closedOnCompletion: {
      type: Boolean,
      default: false,
    },
    requireManualParticipantsNotification: {
      type: Boolean,
      default: false,
    },
    canPotentiallyManageParticipants: {
      required: true,
      type: Boolean,
    },
  },
  data() {
    return {
      openParticipationModal: false,
    };
  },
  computed: {
    initialTab() {
      return this.showAboutOthersTab
        ? this.$id('activities-about-others-tab')
        : this.$id('your-activities-tab');
    },
  },
  mounted() {
    // Show the save notification if we have been redirected back here after saving.
    if (this.completionSaveSuccess) {
      let message = this.closedOnCompletion
        ? 'toast_success_save_close_on_completion_response'
        : 'toast_success_save_response';
      this.showSuccessNotification(message, 'mod_perform');
    }
  },
  methods: {
    /**
     * Shows a success toast.
     * @param {String} message
     * @param {String} component
     */
    showSuccessNotification(message, component) {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str(message, component),
        type: 'success',
      });
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "manage_participation",
      "toast_success_save_close_on_completion_response",
      "toast_success_save_response",
      "user_activities_activities_about_others_title",
      "user_activities_page_title",
      "user_activities_your_activities_title"
    ]
  }
</lang-strings>
