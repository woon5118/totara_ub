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
    <a :href="$url(userActivitiesUrl)">
      {{ $str('back_to_all_activities', 'mod_perform') }}
    </a>

    <PageHeading
      :title="
        $str('user_activities_select_participants_page_title', 'mod_perform')
      "
    />

    <Loader :loading="$apollo.loading">
      <div
        v-if="$apollo.loading || participantSelectionInstances.length > 0"
        class="tui-performUserActivitiesSelectParticipants__content"
      >
        <div>
          {{ $str('user_activities_select_participants_note', 'mod_perform') }}
        </div>

        <ActivityParticipants
          v-for="selectionInstance in participantSelectionInstances"
          :key="selectionInstance.subject_instance.id"
          :subject-instance="selectionInstance.subject_instance"
          :relationships="selectionInstance.manual_relationships"
          :require-input="true"
          :is-saving="savingId === selectionInstance.subject_instance.id"
          @submit="submit($event, selectionInstance.subject_instance.id)"
        >
          <template v-slot:meta>
            <JobAssignmentInformation
              :job-assignments="
                selectionInstance.subject_instance.static_instances
              "
            />
            <p>
              {{
                $str(
                  'user_activities_created_at',
                  'mod_perform',
                  selectionInstance.subject_instance.created_at
                )
              }}
            </p>
          </template>
        </ActivityParticipants>
      </div>

      <div v-else>
        {{ $str('user_activities_select_participants_none', 'mod_perform') }}
      </div>
    </Loader>
  </div>
</template>

<script>
import ActivityParticipants from 'mod_perform/components/user_activities/participant_selector/ActivityParticipants';
import JobAssignmentInformation from 'mod_perform/components/user_activities/participant/JobAssignmentInformation';
import Loader from 'tui/components/loading/Loader';
import PageHeading from 'tui/components/layouts/PageHeading';
import ManualParticipantSelectionInstancesQuery from 'mod_perform/graphql/manual_participant_selection_instances';
import SetManualParticipantsMutation from 'mod_perform/graphql/set_manual_participants';
import { notify } from 'tui/notifications';

export default {
  components: {
    ActivityParticipants,
    JobAssignmentInformation,
    Loader,
    PageHeading,
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
      savingId: null,
    };
  },

  apollo: {
    participantSelectionInstances: {
      query: ManualParticipantSelectionInstancesQuery,
      update: data => data.mod_perform_manual_participant_selection_instances,
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
    async submit(data, subjectInstanceId) {
      this.savingId = subjectInstanceId;

      const participants = data.map(participant => {
        return {
          manual_relationship_id: participant.relationship_id,
          users: participant.users,
        };
      });

      try {
        await this.$apollo.mutate({
          mutation: SetManualParticipantsMutation,
          variables: {
            subject_instance_id: subjectInstanceId,
            participants,
          },
        });

        notify({
          message: this.$str('toast_success_participants_saved', 'mod_perform'),
          type: 'success',
        });
      } catch (e) {
        notify({
          message: this.$str('toast_error_generic_update', 'mod_perform'),
          type: 'error',
        });
      }

      this.savingId = null;

      this.refetch();
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
      "user_activities_created_at",
      "user_activities_select_participants_none",
      "user_activities_select_participants_note",
      "user_activities_select_participants_page_title"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performUserActivitiesSelectParticipants {
  & > * + * {
    margin-top: var(--gap-2);
  }

  &__content {
    & > * + * {
      margin-top: var(--gap-4);
    }
  }
}
</style>
