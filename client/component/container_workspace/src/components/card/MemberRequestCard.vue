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
<template>
  <div class="tui-workspaceMemberRequestCard">
    <div class="tui-workspaceMemberRequestCard__info">
      <a :href="profileUrl" class="tui-workspaceMemberRequestCard__avatar">
        <Avatar
          :src="userProfileImageUrl"
          :alt="userProfileImageAlt || userFullname"
          size="xsmall"
        />
      </a>

      <a
        :href="profileUrl"
        class="tui-workspaceMemberRequestCard__info-userProfile"
      >
        {{ userFullname }}
      </a>
    </div>

    <div
      v-if="!isApproved && !isDeclined"
      class="tui-workspaceMemberRequestCard__actions"
    >
      <LoadingButton
        :loading="submitting.accept"
        :disabled="disabled"
        :aria-label="
          $str('approve_member', 'container_workspace', userFullname)
        "
        :text="$str('approve', 'container_workspace')"
        :primary="true"
        :small="true"
        @click="acceptMemberRequest"
      />

      <LoadingButton
        :loading="submitting.decline"
        :disabled="disabled"
        :aria-label="
          $str('decline_member', 'container_workspace', userFullname)
        "
        :text="$str('decline', 'container_workspace')"
        :small="true"
        @click="declineMemberRequest"
      />
    </div>

    <div v-else>
      <p>
        {{ decisionLabelText }}
      </p>
    </div>
  </div>
</template>

<script>
import Avatar from 'tui/components/avatar/Avatar';
import LoadingButton from 'totara_engage/components/buttons/LoadingButton';
import { notify } from 'tui/notifications';

// GraphQL queries
import acceptMemberRequest from 'container_workspace/graphql/accept_member_request';
import declineMemberRequest from 'container_workspace/graphql/decline_member_request';

export default {
  components: {
    Avatar,
    LoadingButton,
  },

  props: {
    memberRequestId: {
      type: [Number, String],
      required: true,
    },

    userId: {
      type: [Number, String],
      required: true,
    },

    userProfileImageUrl: {
      type: String,
      required: true,
    },

    userProfileImageAlt: String,

    userFullname: {
      type: String,
      required: true,
    },

    isApproved: Boolean,
    isDeclined: Boolean,
  },

  data() {
    return {
      submitting: {
        accept: false,
        decline: false,
      },
    };
  },

  computed: {
    /**
     * @return {Boolean}
     */
    disabled() {
      return this.submitting.accept || this.submitting.decline;
    },

    profileUrl() {
      return this.$url('/user/profile.php', { id: this.userId });
    },

    decisionLabelText() {
      if (this.isDeclined) {
        return this.$str('declined', 'container_workspace');
      }

      return this.$str('approved', 'container_workspace');
    },
  },

  methods: {
    async acceptMemberRequest() {
      if (this.disabled) {
        return;
      }

      this.submitting.accept = true;
      try {
        await this.$apollo.mutate({
          mutation: acceptMemberRequest,
          variables: {
            id: this.memberRequestId,
          },
        });

        this.$emit('accept-request', this.memberRequestId);
      } catch (e) {
        await notify({
          message: this.$str(
            'error:accept_member_request',
            'container_workspace',
            this.userFullname
          ),
          type: 'error',
        });
      } finally {
        this.submitting.accept = false;
      }
    },

    async declineMemberRequest() {
      if (this.disabled) {
        return;
      }

      this.submitting.decline = true;
      try {
        await this.$apollo.mutate({
          mutation: declineMemberRequest,
          variables: {
            id: this.memberRequestId,
          },
        });

        this.$emit('decline-request', this.memberRequestId);
      } catch (e) {
        await notify({
          message: this.$str(
            'error:decline_member_request',
            'container_workspace',
            this.userFullname
          ),
          type: 'error',
        });
      } finally {
        this.submitting.decline = false;
      }
    },
  },
};
</script>

<lang-strings>
{
  "container_workspace": [
    "approve",
    "decline",
    "approve_member",
    "decline_member",
    "error:accept_member_request",
    "error:decline_member_request",
    "approved",
    "declined"
  ]
}
</lang-strings>

<style lang="scss">
.tui-workspaceMemberRequestCard {
  display: flex;
  align-items: center;

  &__info {
    display: flex;
    flex: 1;
    align-items: center;

    &-userProfile {
      @include tui-font-link-small();
      @include tui-font-heavy();

      margin-left: var(--gap-2);
    }
  }

  &__actions {
    display: flex;
    align-items: center;

    > :not(:first-child) {
      margin-left: var(--gap-4);
    }
  }
}
</style>
