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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package totara_perform
-->

<template>
  <div class="tui-performActivityActions__actionIcons">
    <a
      v-if="activity.can_view_participation_reporting"
      :href="participationReportingUrl"
      :title="$str('participation_reporting', 'mod_perform')"
    >
      <ParticipationReportingIcon
        :alt="$str('participation_reporting', 'mod_perform')"
        :title="$str('participation_reporting', 'mod_perform')"
        size="200"
      />
    </a>

    <Dropdown v-if="activity.can_manage" position="bottom-right">
      <template v-slot:trigger="{ toggle }">
        <a href="#" @click.prevent="toggle">
          <ActivityActionsIcon
            :alt="$str('activity_action_options', 'mod_perform')"
            :title="$str('activity_action_options', 'mod_perform')"
            size="200"
          />
        </a>
      </template>
      <DropdownItem
        v-if="activity.can_potentially_activate"
        :disabled="!activity.can_activate"
        :title="activateOptionTitle"
        @click="showActivateModal"
      >
        {{ $str('activity_action_activate', 'mod_perform') }}
      </DropdownItem>
      <DropdownItem @click="showDeleteModal">
        {{ $str('activity_action_delete', 'mod_perform') }}
      </DropdownItem>
    </Dropdown>

    <ConfirmationModal
      :open="activateModalOpen"
      :title="$str('modal_activate_title', 'mod_perform')"
      :confirm-button-text="$str('activity_action_activate', 'mod_perform')"
      :loading="activating"
      @confirm="activateActivity"
      @cancel="closeActivateModal"
    >
      <Loader :loading="$apollo.queries.activityUsersToAssignCount.loading">
        <span
          v-html="
            $str(
              'modal_activate_message',
              'mod_perform',
              activityUsersToAssignCount
            )
          "
        />
      </Loader>
    </ConfirmationModal>

    <ConfirmationModal
      :open="deleteModalOpen"
      :title="deleteConfirmationTitle"
      :confirm-button-text="$str('delete')"
      :loading="deleting"
      @confirm="deleteActivity"
      @cancel="closeDeleteModal"
    >
      <template v-if="activityIsDraft">
        <p>{{ $str('modal_delete_draft_message', 'mod_perform') }}</p>
      </template>
      <template v-else>
        <p>{{ $str('modal_delete_message', 'mod_perform') }}</p>
        <p>
          <strong>{{
            $str('modal_delete_message_data_recovery_warning', 'mod_perform')
          }}</strong>
        </p>
      </template>
      <p>{{ $str('modal_delete_confirmation_line', 'mod_perform') }}</p>
    </ConfirmationModal>
  </div>
</template>

<script>
import ActivateActivityMutation from 'mod_perform/graphql/activate_activity.graphql';
import ActivateDeleteMutation from 'mod_perform/graphql/delete_activity.graphql';
import ActivityActionsIcon from 'mod_perform/components/icons/ActivityActions';
import ActivityUsersToAssignCountQuery from 'mod_perform/graphql/activity_users_to_assign_count.graphql';
import ConfirmationModal from 'totara_core/components/modal/ConfirmationModal';
import Dropdown from 'totara_core/components/dropdown/Dropdown';
import DropdownItem from 'totara_core/components/dropdown/DropdownItem';
import Loader from 'totara_core/components/loader/Loader';
import ParticipationReportingIcon from 'mod_perform/components/icons/ParticipationReporting';
import { notify } from 'totara_core/notifications';
import { NOTIFICATION_DURATION } from 'mod_perform/constants';

export default {
  components: {
    ActivityActionsIcon,
    ConfirmationModal,
    Dropdown,
    DropdownItem,
    Loader,
    ParticipationReportingIcon,
  },

  props: {
    activity: {
      required: true,
      type: Object,
    },
  },

  data() {
    return {
      activateModalOpen: false,
      activityUsersToAssignCount: 0,
      deleteModalOpen: false,
      activating: false,
      deleting: false,
    };
  },

  computed: {
    /**
     * Get the url to the participation tracking
     *
     * @return {string}
     */
    participationReportingUrl() {
      return this.$url('/mod/perform/reporting/participation/index.php', {
        activity_id: this.activity.id,
      });
    },

    /**
     * For certain cases, get a title text for the 'Activate" dropdown option.
     */
    activateOptionTitle() {
      if (
        this.activity.can_potentially_activate &&
        !this.activity.can_activate
      ) {
        return this.$str('activity_draft_not_ready', 'mod_perform');
      }
      return this.$str('activity_action_activate', 'mod_perform');
    },

    /**
     * Activity state dependant delete confirmation title.
     *
     * @return {string}
     */
    deleteConfirmationTitle() {
      if (this.activityIsDraft) {
        return this.$str('modal_delete_draft_title', 'mod_perform');
      }

      return this.$str('modal_delete_title', 'mod_perform');
    },

    /**
     * Is the activity in draft state.
     *
     * @return {boolean}
     */
    activityIsDraft() {
      return this.activity.state_details.name === 'DRAFT';
    },
  },

  methods: {
    /**
     * Display the modal for confirming the activation of the activity.
     */
    showActivateModal() {
      this.activateModalOpen = true;
    },

    /**
     * Close the modal for confirming the activation of the activity.
     */
    closeActivateModal() {
      this.activateModalOpen = false;
      this.activating = false;
    },

    /**
     * Activate an activity
     */
    activateActivity() {
      this.activating = true;

      this.$apollo
        .mutate({
          mutation: ActivateActivityMutation,
          variables: {
            input: {
              activity_id: this.activity.id,
            },
          },
        })
        .then(() => {
          notify({
            duration: NOTIFICATION_DURATION,
            message: this.$str(
              'toast_success_activity_activated',
              'mod_perform',
              this.activity.name
            ),
            type: 'success',
          });
          this.$emit('refetch');
          this.closeActivateModal();
        })
        .catch(() => {
          this.showErrorNotification();
          this.$emit('refetch');
          this.closeActivateModal();
        });
    },

    /**
     * Display the modal for confirming the deletion of the activity.
     */
    showDeleteModal() {
      this.deleteModalOpen = true;
    },

    /**
     * Close the modal for confirming the deletion of the activity.
     */
    closeDeleteModal() {
      this.deleteModalOpen = false;
      this.deleting = false;
    },

    /**
     * Deletes the activity.
     */
    async deleteActivity() {
      this.deleting = true;

      try {
        await this.$apollo.mutate({
          mutation: ActivateDeleteMutation,
          variables: {
            input: {
              activity_id: this.activity.id,
            },
          },
        });

        this.showDeleteSuccessNotification();
      } catch (e) {
        this.showErrorNotification();
      }

      this.$emit('refetch');
      this.closeDeleteModal();
    },

    showDeleteSuccessNotification() {
      let message = this.$str('toast_success_activity_deleted', 'mod_perform');

      if (this.activityIsDraft) {
        message = this.$str(
          'toast_success_draft_activity_deleted',
          'mod_perform'
        );
      }

      notify({
        duration: NOTIFICATION_DURATION,
        message,
        type: 'success',
      });
    },

    /**
     * Show generic save/update error toast.
     */
    showErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str(
          'toast_error_generic_update',
          'mod_perform',
          this.activity.name
        ),
        type: 'error',
      });
    },
  },

  apollo: {
    activityUsersToAssignCount: {
      query: ActivityUsersToAssignCountQuery,
      variables() {
        return {
          activity_id: this.activity.id,
        };
      },
      update: data => data.mod_perform_activity_users_to_assign_count,
      skip() {
        return !this.activateModalOpen || this.activating;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_action_activate",
      "activity_action_delete",
      "activity_action_options",
      "activity_draft_not_ready",
      "modal_activate_message",
      "modal_activate_title",
      "modal_delete_confirmation_line",
      "modal_delete_draft_message",
      "modal_delete_draft_title",
      "modal_delete_message",
      "modal_delete_message_data_recovery_warning",
      "modal_delete_title",
      "participation_reporting",
      "participation_reporting",
      "toast_error_generic_update",
      "toast_success_activity_activated",
      "toast_success_activity_deleted",
      "toast_success_draft_activity_deleted"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
