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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module container_workspace
-->
<!-- All the workspace related actions are here -->
<template>
  <div
    class="tui-workspacePrimaryAction"
    :class="{
      'tui-workspacePrimaryAction--initialised': $apollo.loading,
    }"
  >
    <ModalPresenter
      :open="modal.leaveConfirm"
      @request-close="modal.leaveConfirm = false"
    >
      <WorkspaceWarningModal
        :title="$str('delete_warning_title', 'container_workspace')"
        :message-content="
          $str('leave_workspace_message', 'container_workspace')
        "
        :confirm-button-text="$str('leave', 'container_workspace')"
        @confirm="leaveWorkspace"
      />
    </ModalPresenter>

    <ModalPresenter
      :open="modal.deleteConfirm"
      @request-close="modal.deleteConfirm = false"
    >
      <WorkspaceWarningModal
        :title="$str('delete_warning_title', 'container_workspace')"
        :message-content="$str('delete_warning_msg', 'container_workspace')"
        :confirm-button-text="$str('delete_workspace', 'container_workspace')"
        @confirm="handleDelete"
      />
    </ModalPresenter>

    <ModalPresenter :open="modal.edit" @request-close="modal.edit = false">
      <WorkspaceEditModal
        :workspace-id="workspaceId"
        @update-workspace="updateWorkspace"
      />
    </ModalPresenter>

    <ModalPresenter
      :open="modal.transferOwner"
      @request-close="modal.transferOwner = false"
    >
      <WorkspaceTransferOwnerModal
        :workspace-id="workspaceId"
        @transfered-owner="modal.transferOwner = false"
      />
    </ModalPresenter>

    <!-- We are only enable the user adder if the actor is either owner or a site admin -->
    <WorkspaceUserAdder
      v-if="interactor.own || interactor.workspaces_admin"
      :open="modal.adder"
      :workspace-id="workspaceId"
      @cancel="modal.adder = false"
      @add-members="handleAddMembers"
    />

    <Loading v-if="$apollo.loading" />

    <template v-else>
      <!-- Owner section -->
      <Dropdown
        v-if="interactor.own || interactor.workspaces_admin"
        position="bottom-right"
        class="tui-workspacePrimaryAction__dropDown"
      >
        <template v-slot:trigger="{ toggle, isOpen }">
          <Button
            :text="
              interactor.own
                ? $str('owner', 'container_workspace')
                : $str('admin', 'moodle')
            "
            :aria-expanded="isOpen"
            :caret="true"
            class="tui-workspacePrimaryAction__dropDown__button"
            @click.prevent="toggle"
          />
        </template>

        <DropdownItem v-if="interactor.can_join" @click="joinWorkspace">
          {{ $str('join_workspace', 'container_workspace') }}
        </DropdownItem>

        <DropdownItem
          v-if="interactor.can_leave"
          @click="modal.leaveConfirm = true"
        >
          {{ $str('leave_workspace', 'container_workspace') }}
        </DropdownItem>

        <DropdownItem @click="modal.adder = true">
          {{ $str('add_members', 'container_workspace') }}
        </DropdownItem>

        <DropdownItem v-if="interactor.can_update" @click="modal.edit = true">
          {{ $str('edit_space', 'container_workspace') }}
        </DropdownItem>

        <DropdownItem
          v-if="interactor.joined"
          @click="updateMuteStatus(!interactor.muted)"
        >
          <!-- Drop down item to toggle the mute status -->
          <template v-if="!interactor.muted">
            {{ $str('mute_notifications', 'container_workspace') }}
          </template>
          <template v-else>
            {{ $str('unmute_notifications', 'container_workspace') }}
          </template>
        </DropdownItem>

        <DropdownItem
          v-if="interactor.can_transfer_ownership"
          @click="modal.transferOwner = true"
        >
          {{ $str('transfer_ownership', 'container_workspace') }}
        </DropdownItem>

        <DropdownItem
          v-if="interactor.can_delete"
          @click="modal.deleteConfirm = true"
        >
          {{ $str('delete_workspace', 'container_workspace') }}
        </DropdownItem>
      </Dropdown>
      <!-- End of owner section -->

      <!-- Normal user section -->
      <template v-else>
        <!-- A normal user interactor - non owner nor admin -->
        <LoadingButton
          v-if="!interactor.joined && interactor.can_join"
          :loading="innerSubmitting"
          :text="$str('join_workspace', 'container_workspace')"
          :aria-disabled="innerSubmitting"
          :aria-label="$str('join_space', 'container_workspace', workspaceName)"
          class="tui-workspacePrimaryAction__button"
          @click="joinWorkspace"
        />

        <!--
          Button to cancel the created request to join workspace. This has to be put before request to join button
          as you are still able to request to join the workspace even though you had already requested
         -->
        <LoadingButton
          v-else-if="!interactor.joined && interactor.has_requested_to_join"
          :loading="innerSubmitting"
          :text="$str('cancel_request', 'container_workspace')"
          :aria-disabled="innerSubmitting"
          :aria-label="$str('cancel_request', 'container_workspace')"
          class="tui-workspacePrimaryAction__button"
          @click="cancelRequestToJoinWorkspace"
        />

        <!-- Button to request to join workspace -->
        <LoadingButton
          v-else-if="!interactor.joined && interactor.can_request_to_join"
          :loading="innerSubmitting"
          :text="$str('request_to_join', 'container_workspace')"
          :aria-disabled="innerSubmitting"
          :aria-label="$str('request_to_join', 'container_workspace')"
          class="tui-workspacePrimaryAction__button"
          @click="requestToJoinWorkspace"
        />

        <Dropdown
          v-else
          position="bottom-right"
          class="tui-workspacePrimaryAction__dropDown"
        >
          <template v-slot:trigger="{ toggle, isOpen }">
            <Button
              :text="$str('joined', 'container_workspace')"
              :caret="true"
              :aria-expanded="isOpen"
              class="tui-workspacePrimaryAction__dropDown__button"
              @click.prevent="toggle"
            />
          </template>

          <DropdownItem
            v-if="interactor.can_leave"
            @click="modal.leaveConfirm = true"
          >
            {{ $str('leave_workspace', 'container_workspace') }}
          </DropdownItem>
          <DropdownItem @click="updateMuteStatus(!interactor.muted)">
            <!-- Drop down item to toggle the mute status -->
            <template v-if="!interactor.muted">
              {{ $str('mute_notifications', 'container_workspace') }}
            </template>
            <template v-else>
              {{ $str('unmute_notifications', 'container_workspace') }}
            </template>
          </DropdownItem>
        </Dropdown>
      </template>
      <!-- End of normal user section -->
    </template>
  </div>
</template>

<script>
import Button from 'tui/components/buttons/Button';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import WorkspaceWarningModal from 'container_workspace/components/modal/WorkspaceWarningModal';
import LoadingButton from 'totara_engage/components/buttons/LoadingButton';
import Loading from 'tui/components/icons/Loading';
import { notify } from 'tui/notifications';
import Dropdown from 'tui/components/dropdown/Dropdown';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import WorkspaceEditModal from 'container_workspace/components/modal/WorkspaceEditModal';
import WorkspaceUserAdder from 'container_workspace/components/adder/WorkspaceUserAdder';
import WorkspaceTransferOwnerModal from 'container_workspace/components/modal/WorkspaceTransferOwnerModal';

// GraphQL queries
import getWorkspaceInteractor from 'container_workspace/graphql/workspace_interactor';
import joinWorkspace from 'container_workspace/graphql/join_workspace';
import leaveWorkspace from 'container_workspace/graphql/leave_workspace';
import deleteWorkspace from 'container_workspace/graphql/delete_workspace';
import requestToJoinWorkspace from 'container_workspace/graphql/request_to_join';
import cancelMemberRequest from 'container_workspace/graphql/cancel_member_request';
import addMembers from 'container_workspace/graphql/add_members';
import muteWorkspace from 'container_workspace/graphql/mute_workspace';
import unmuteWorkspace from 'container_workspace/graphql/unmute_workspace';

export default {
  components: {
    Button,
    ModalPresenter,
    WorkspaceWarningModal,
    LoadingButton,
    Loading,
    DropdownItem,
    Dropdown,
    WorkspaceEditModal,
    WorkspaceUserAdder,
    WorkspaceTransferOwnerModal,
  },

  props: {
    workspaceId: {
      type: [String, Number],
      required: true,
    },

    workspaceName: {
      type: String,
      required: true,
    },
  },

  apollo: {
    interactor: {
      query: getWorkspaceInteractor,
      variables() {
        return {
          workspace_id: this.workspaceId,
        };
      },
    },
  },

  data() {
    return {
      interactor: {},
      innerSubmitting: false,
      modal: {
        leaveConfirm: false,
        deleteConfirm: false,
        edit: false,
        adder: false,
        transferOwner: false,
      },
    };
  },

  methods: {
    async joinWorkspace() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        await this.$apollo.mutate({
          mutation: joinWorkspace,
          refetchAll: true,
          variables: {
            workspace_id: this.workspaceId,
          },
        });

        this.$emit('join-workspace');
      } catch (e) {
        await notify({
          message: this.$str('error:join_space', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    async leaveWorkspace() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;

      try {
        await this.$apollo.mutate({
          mutation: leaveWorkspace,
          refetchAll: true,
          variables: {
            workspace_id: this.workspaceId,
          },
        });

        this.modal.leaveConfirm = false;
        this.$emit('leave-workspace');
      } catch (e) {
        await notify({
          message: this.$str('error:leave_workspace', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    async handleDelete() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;

      try {
        const {
          data: { result },
        } = await this.$apollo.mutate({
          mutation: deleteWorkspace,
          refetchAll: false,
          variables: {
            workspace_id: this.workspaceId,
          },
        });

        if (result) {
          this.modal.confirm = false;
          this.$emit('deleted-workspace');
        }
      } catch (e) {
        await notify({
          message: this.$str('error:delete_workspace', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    /**
     * Chaining the event up to parent. And in the same time hide the modal.
     *
     * @param {Object} workspace
     */
    updateWorkspace(workspace) {
      this.modal.edit = false;
      this.$emit('update-workspace', workspace);
    },

    async requestToJoinWorkspace() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        await this.$apollo.mutate({
          mutation: requestToJoinWorkspace,
          variables: {
            workspace_id: this.workspaceId,
          },

          update: store => {
            let { interactor } = store.readQuery({
              query: getWorkspaceInteractor,
              variables: {
                workspace_id: this.workspaceId,
              },
            });

            interactor = Object.assign({}, interactor, {
              has_requested_to_join: true,
              can_request_to_join: true,
            });

            store.writeQuery({
              query: getWorkspaceInteractor,
              variables: {
                workspace_id: this.workspaceId,
              },

              data: { interactor },
            });
          },
        });

        this.$emit('request-to-join-workspace');
      } catch (e) {
        await notify({
          message: this.$str('error:request_to_join', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    async cancelRequestToJoinWorkspace() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;

      try {
        await this.$apollo.mutate({
          mutation: cancelMemberRequest,
          variables: {
            workspace_id: this.workspaceId,
          },

          update: store => {
            let { interactor } = store.readQuery({
              query: getWorkspaceInteractor,
              variables: {
                workspace_id: this.workspaceId,
              },
            });

            interactor = Object.assign({}, interactor);
            interactor.can_request_to_join = true;
            interactor.has_requested_to_join = false;

            store.writeQuery({
              query: getWorkspaceInteractor,
              variables: {
                workspace_id: this.workspaceId,
              },
              data: { interactor },
            });
          },
        });

        this.$emit('cancel-request-to-join-workspace');
      } catch (e) {
        await notify({
          message: this.$str(
            'error:cancel_member_request',
            'container_workspace'
          ),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    /**
     *
     * @param {Number[]} userIds
     */
    async handleAddMembers(userIds) {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        await this.$apollo.mutate({
          mutation: addMembers,
          variables: {
            workspace_id: this.workspaceId,
            user_ids: userIds,
          },
          refetchQueries: [
            'container_workspace_get_workspace',
            'container_workspace_find_members',
          ],
        });

        this.modal.adder = false;
        this.$emit('added-member');
      } catch (e) {
        await notify({
          message: this.$str('error:add_members', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    /**
     *
     * @param {Boolean} status
     * @return {Promise<void>}
     */
    async updateMuteStatus(status) {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;

      try {
        const variables = {
          workspace_id: this.workspaceId,
        };

        let mutation = status ? muteWorkspace : unmuteWorkspace;
        await this.$apollo.mutate({
          mutation,
          variables,
          update: proxy => {
            proxy.writeQuery({
              query: getWorkspaceInteractor,
              variables: variables,
              data: {
                interactor: Object.assign({}, this.interactor, {
                  muted: status,
                }),
              },
            });
          },
        });

        this.$emit('update-mute-status', status);
      } finally {
        this.innerSubmitting = false;
      }
    },
  },
};
</script>

<lang-strings>
{
  "container_workspace": [
    "joined",
    "join_workspace",
    "join_space",
    "leave",
    "delete_warning_title",
    "leave_workspace",
    "leave_workspace_message",
    "error:join_space",
    "error:leave_space",
    "owner",
    "delete_workspace",
    "delete_warning_msg",
    "error:delete_workspace",
    "edit_space",
    "request_to_join",
    "cancel_request",
    "error:request_to_join",
    "error:cancel_member_request",
    "add_members",
    "mute_notifications",
    "unmute_notifications",
    "transfer_ownership",
    "error:add_members"
  ],
  "moodle": [
    "admin"
  ]
}
</lang-strings>

<style lang="scss">
.tui-workspacePrimaryAction {
  display: flex;
  width: 100%;

  @media (min-width: $tui-screen-sm) {
    justify-content: flex-end;
  }

  &--initialise {
    justify-content: center;
    padding: var(--gap-2);
  }

  &__dropDown {
    width: 100%;

    @media (min-width: $tui-screen-sm) {
      // IE support - :(
      width: auto;
    }

    &__button {
      width: 100%;

      @media (min-width: $tui-screen-sm) {
        // IE support - :(
        width: auto;
      }
    }
  }

  &__button {
    width: 100%;

    @media (min-width: $tui-screen-sm) {
      // IE Support - :(
      width: auto;
    }
  }
}
</style>
