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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui-performUserActivitiesSelectParticipants">
    <a :href="$url(userActivitiesUrl)">{{
      $str('back_to_all_activities', 'mod_perform')
    }}</a>

    <h2 class="tui-performUserActivitiesSelectParticipants__header">
      {{
        $str('user_activities_select_participants_page_title', 'mod_perform')
      }}
    </h2>

    <Loader
      v-if="$apollo.loading || participantSelectionInstances.length > 0"
      :loading="$apollo.loading"
    >
      <p>
        {{ $str('user_activities_select_participants_note', 'mod_perform') }}
      </p>

      <ActivityParticipants
        v-for="selectionInstance in participantSelectionInstances"
        :key="selectionInstance.subject_instance.id"
        :subject-instance="selectionInstance.subject_instance"
        :relationships="selectionInstance.manual_relationships"
        :users="users"
        :current-user-id="currentUserId"
        @submit="submit"
        @error="error"
      />
    </Loader>

    <p v-else>
      {{ $str('user_activities_select_participants_none', 'mod_perform') }}
    </p>
  </div>
</template>

<script>
import ActivityParticipants from 'mod_perform/components/user_activities/participant_selector/ActivityParticipants';
import Loader from 'totara_core/components/loader/Loader';
import ManualParticipantSelectionInstancesQuery from 'mod_perform/graphql/manual_participant_selection_instances';
import SelectableUsersQuery from 'mod_perform/graphql/selectable_users';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';
import { notify } from 'totara_core/notifications';

export default {
  components: {
    ActivityParticipants,
    Loader,
  },

  props: {
    currentUserId: {
      required: true,
      type: Number,
    },
    userActivitiesUrl: {
      required: true,
      type: String,
    },
  },

  data() {
    return {
      participantSelectionInstances: [],
      users: [],
    };
  },

  apollo: {
    participantSelectionInstances: {
      query: ManualParticipantSelectionInstancesQuery,
      update: data => data.mod_perform_manual_participant_selection_instances,
    },
    users: {
      query: SelectableUsersQuery,
      update: data => data.mod_perform_selectable_users,
    },
  },

  methods: {
    /**
     * Reload the activities query.
     */
    refetch() {
      this.$apollo.queries.participantSelectionInstances.refetch();
    },

    /**
     * Reload the activities query and show a success notification.
     */
    submit() {
      this.refetch();

      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_success_participants_saved', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Reload the activities query and show an error notification.
     */
    error() {
      this.refetch();

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
      "toast_error_generic_update",
      "toast_success_participants_saved",
      "user_activities_select_participants_none",
      "user_activities_select_participants_note",
      "user_activities_select_participants_page_title"
    ]
  }
</lang-strings>
