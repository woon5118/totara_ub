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
import Button from 'totara_core/components/buttons/Button';
import SelectActivityModal from 'mod_perform/components/manage_activity/participation/SelectActivityModal';
import Tab from 'totara_core/components/tabs/Tab';
import Tabs from 'totara_core/components/tabs/Tabs';
import UserActivityList from 'mod_perform/components/user_activities/list/Activities';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
import { notify } from 'totara_core/notifications';

export default {
  components: {
    Button,
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
      "toast_success_save_close_on_completion_response",
      "toast_success_save_response",
      "user_activities_activities_about_others_title",
      "user_activities_page_title",
      "user_activities_your_activities_title",
      "manage_participation"
    ]
  }
</lang-strings>
