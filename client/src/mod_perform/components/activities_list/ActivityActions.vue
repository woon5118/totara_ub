<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performActivityActions__actionIcons">
    <a
      v-if="activityIsDraft"
      :href="participationManagementUrl"
      :title="$str('manage_participation', 'mod_perform')"
      class="tui-performActivityActions__actionIcons__link"
    >
      <Users
        :alt="$str('manage_participation', 'mod_perform')"
        :title="$str('manage_participation', 'mod_perform')"
        size="200"
      />
    </a>
    <a
      v-if="activity.can_view_participation_reporting"
      :href="participationReportingUrl"
      :title="$str('participation_reporting', 'mod_perform')"
      class="tui-performActivityActions__actionIcons__link"
    >
      <ParticipationReportingIcon
        :alt="$str('participation_reporting', 'mod_perform')"
        :title="$str('participation_reporting', 'mod_perform')"
        size="200"
      />
    </a>

    <Dropdown v-if="activity.can_manage" position="bottom-right">
      <template v-slot:trigger="{ toggle }">
        <MoreButton
          :aria-label="$str('activity_action_options', 'mod_perform')"
          @click="toggle"
        />
      </template>
      <DropdownItem
        v-if="activity.can_potentially_activate"
        @click="$refs.activateActivityModal.open()"
      >
        {{ $str('activity_action_activate', 'mod_perform') }}
      </DropdownItem>
      <DropdownItem v-if="activity.can_clone" @click="cloneActivity">
        {{ $str('activity_action_clone', 'mod_perform') }}
      </DropdownItem>
      <DropdownItem @click="showDeleteModal">
        {{ $str('activity_action_delete', 'mod_perform') }}
      </DropdownItem>
    </Dropdown>

    <ActivateActivityModal
      v-if="activity.can_potentially_activate"
      ref="activateActivityModal"
      :activity="activity"
      @refetch="$emit('refetch')"
    />

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
import ActivateActivityModal from 'mod_perform/components/manage_activity/ActivateActivityModal';
import Users from 'tui/components/icons/common/Users';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import MoreButton from 'tui/components/buttons/MoreIcon';
import ParticipationReportingIcon from 'mod_perform/components/icons/ParticipationReporting';
import { notify } from 'tui/notifications';
import {
  ACTIVITY_STATUS_DRAFT,
  NOTIFICATION_DURATION,
} from 'mod_perform/constants';

// Queries
import activateCloneMutation from 'mod_perform/graphql/clone_activity';
import activateDeleteMutation from 'mod_perform/graphql/delete_activity';

export default {
  components: {
    ActivateActivityModal,
    Users,
    ConfirmationModal,
    Dropdown,
    DropdownItem,
    MoreButton,
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
      deleteModalOpen: false,
      deleting: false,
    };
  },

  computed: {
    /**
     * Is the activity in draft state.
     *
     * @return {boolean}
     */
    activityIsDraft() {
      return this.activity.state_details.name === ACTIVITY_STATUS_DRAFT;
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
     * Get the url to the participation management
     *
     * @return {string}
     */
    participationManagementUrl() {
      return this.$url(
        '/mod/perform/manage/participation/subject_instances.php',
        {
          activity_id: this.activity.id,
        }
      );
    },

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
  },

  methods: {
    /**
     * Clones the activity.
     */
    async cloneActivity() {
      try {
        await this.$apollo.mutate({
          mutation: activateCloneMutation,
          variables: {
            input: {
              activity_id: this.activity.id,
            },
          },
        });
        this.showCloneSuccessNotification();
      } catch (e) {
        this.showErrorNotification();
      }
      this.$emit('refetch');
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
          mutation: activateDeleteMutation,
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

    showCloneSuccessNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str(
          'toast_success_activity_cloned',
          'mod_perform',
          this.activity.name
        ),
        type: 'success',
      });
    },

    /**
     * Display the modal for confirming the deletion of the activity.
     */
    showDeleteModal() {
      this.deleteModalOpen = true;
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
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_action_activate",
      "activity_action_clone",
      "activity_action_delete",
      "activity_action_options",
      "manage_participation",
      "modal_delete_confirmation_line",
      "modal_delete_draft_message",
      "modal_delete_draft_title",
      "modal_delete_message",
      "modal_delete_message_data_recovery_warning",
      "modal_delete_title",
      "participation_reporting",
      "toast_error_generic_update",
      "toast_success_activity_cloned",
      "toast_success_activity_deleted",
      "toast_success_draft_activity_deleted"
    ],
    "moodle": [
      "delete"
    ]
  }
</lang-strings>
