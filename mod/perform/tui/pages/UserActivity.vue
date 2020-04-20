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
  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-performUserActivity">
    <Loader :loading="$apollo.loading">
      <NotificationBanner
        v-if="activityNotFound"
        :dismissable="false"
        :message="
          $str('user_activities_activity_does_not_exist', 'mod_perform')
        "
        type="error"
      />
      <ActivityContent
        v-if="activityFound"
        :subject-instance="subjectInstance"
      />
    </Loader>
  </div>
</template>

<script>
import subjectInstanceQuery from 'mod_perform/graphql/subject_instance.graphql';
import Loader from 'totara_core/components/loader/Loader';
import NotificationBanner from 'totara_core/components/notifications/NotificationBanner';
import ActivityContent from 'mod_perform/components/user_activities/ActivityContent';

export default {
  components: {
    Loader,
    NotificationBanner,
    ActivityContent,
  },
  props: {
    subjectInstanceId: {
      required: true,
      type: Number,
    },
  },

  data() {
    return {
      hasLoaded: false,
      userActivity: null,
      subjectInstance: null,
    };
  },
  apollo: {
    subjectInstance: {
      query: subjectInstanceQuery,
      variables() {
        return {
          subject_instance_id: this.subjectInstanceId,
        };
      },
      update(data) {
        this.hasLoaded = true;

        return data['mod_perform_subject_instance'];
      },
    },
  },
  computed: {
    /**
     * Trying to load the activity from the server resulted in either
     * the activity not being found at all or the user not having permission
     * to view the activity.
     */
    activityNotFound() {
      return !this.$apollo.loading && this.subjectInstance === null;
    },
    activityFound() {
      return !this.$apollo.loading && this.subjectInstance !== null;
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "user_activities_activity_does_not_exist"
    ]
  }
</lang-strings>
