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

  @author Kian Nguyen <kian.nguyen@totaralearning.com>
  @module container_workspace
-->
<template>
  <article
    class="tui-workspaceMemberCard"
    :aria-describedby="owner ? $id('lozenge') : false"
  >
    <MiniProfileCard
      :display="userCardDisplay"
      :label-id="labelId"
      :drop-down-button-aria-label="
        $str('more_action_for_member', 'container_workspace', userFullName)
      "
      :aria-describedby="owner ? $id('lozenge') : null"
      class="tui-workspaceMemberCard__profileCard"
    >
      <template v-slot:tag>
        <Lozenge
          v-if="owner"
          :id="$id('lozenge')"
          :text="$str('owner', 'container_workspace')"
          type="neutral"
          class="tui-workspaceMemberCard__profileCard__tag"
        />
      </template>

      <template v-if="deleteAble" v-slot:drop-down-items>
        <DropdownItem @click="modal.confirm = true">
          {{ $str('remove', 'moodle') }}
        </DropdownItem>
      </template>
    </MiniProfileCard>

    <ModalPresenter
      :open="modal.confirm"
      @request-close="modal.confirm = false"
    >
      <WorkspaceWarningModal
        :title="$str('delete_warning_title', 'container_workspace')"
        :message-content="
          $str('remove_member_warning_msg', 'container_workspace', userFullName)
        "
        @confirm="removeMember"
      />
    </ModalPresenter>
  </article>
</template>

<script>
import MiniProfileCard from 'tui/components/profile/MiniProfileCard';
import { notify } from 'tui/notifications';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import WorkspaceWarningModal from 'container_workspace/components/modal/WorkspaceWarningModal';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import Lozenge from 'tui/components/lozenge/Lozenge';

//GraphQL
import removeMemberFromWorkspace from 'container_workspace/graphql/remove_member_from_workspace';
import getWorkspace from 'container_workspace/graphql/get_workspace';

export default {
  components: {
    MiniProfileCard,
    ModalPresenter,
    WorkspaceWarningModal,
    DropdownItem,
    Lozenge,
  },

  props: {
    userFullName: {
      type: String,
      required: true,
    },

    userCardDisplay: {
      type: Object,
      required: true,
    },

    deleteAble: {
      type: Boolean,
      default: false,
    },

    /**
     * The user's id of a member.
     */
    userId: {
      type: [String, Number],
      required: true,
    },

    workspaceId: {
      type: [String, Number],
      required: true,
    },

    labelId: {
      type: String,
      required: true,
    },

    owner: Boolean,
  },

  data() {
    return {
      modal: {
        confirm: false,
      },
    };
  },

  methods: {
    async removeMember() {
      try {
        await this.$apollo.mutate({
          mutation: removeMemberFromWorkspace,
          refetchAll: false,
          variables: {
            workspace_id: this.workspaceId,
            user_id: this.userId,
          },

          refetchQueries: [
            {
              query: getWorkspace,
              variables: {
                id: this.workspaceId,
              },
            },
          ],
        });

        this.modal.confirm = false;
        this.$emit('remove-member', this.userId);
      } catch (e) {
        await notify({
          message: this.$str('error:remove_user', 'container_workspace'),
          type: 'error',
        });
      }
    },
  },
};
</script>
<lang-strings>
  {
    "moodle": [
      "remove"
    ],
    "container_workspace": [
      "error:remove_user",
      "delete_warning_title",
      "remove_member_warning_msg",
      "more_action_for_member",
      "owner"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-workspaceMemberCard {
  &__profileCard {
    &.tui-miniProfileCard {
      width: 100%;
    }

    &__tag {
      margin-left: var(--gap-1);
    }
  }
}
</style>
