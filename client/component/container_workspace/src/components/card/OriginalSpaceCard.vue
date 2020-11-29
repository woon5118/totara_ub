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
  <div
    class="tui-originalSpaceCard"
    :style="{
      'background-image': `url('${image}')`,
    }"
    @click="handleClick(url)"
  >
    <div class="tui-originalSpaceCard__titleBox">
      <h3 class="tui-originalSpaceCard__title">
        <a class="tui-originalSpaceCard__link" :href="url">
          {{ title }}
        </a>
      </h3>
    </div>

    <div class="tui-originalSpaceCard__actions">
      <LoadingButton
        v-if="!joined && joinAble"
        ref="joinButtonRef"
        class="tui-originalSpaceCard__actions-button"
        :text="$str('join_me', 'container_workspace')"
        :aria-label="$str('join_space', 'container_workspace', title)"
        :loading="submitting"
        @click.stop="joinWorkspace"
      />

      <Button
        v-else-if="!joined && hasRequestedToJoin"
        :text="$str('requested_to_join', 'container_workspace')"
        class="tui-originalSpaceCard__actions-button"
        :disabled="true"
      />

      <LoadingButton
        v-else-if="!joined && requestToJoinAble"
        :text="$str('request_to_join', 'container_workspace')"
        class="tui-originalSpaceCard__actions-button"
        :aria-label="
          $str('request_to_join_space', 'container_workspace', title)
        "
        :loading="submitting"
        @click.stop="requestToJoinWorkspace"
      />

      <!-- If the actor is an owner then this button will be disabled for them -->
      <Dropdown
        v-else-if="joined"
        :disabled="owned"
        class="tui-originalSpaceCard__actions-dropDown"
      >
        <template v-slot:trigger="{ isOpen, toggle }">
          <Button
            ref="joinedButtonRef"
            :text="$str('joined', 'container_workspace')"
            :caret="!owned"
            :aria-expanded="isOpen"
            :disabled="owned"
            @click.stop="toggle"
          />
        </template>

        <DropdownItem @click.stop.prevent="leaveWorkspace">
          {{ $str('leave_workspace', 'container_workspace') }}
        </DropdownItem>
      </Dropdown>
    </div>
  </div>
</template>

<script>
import LoadingButton from 'totara_engage/components/buttons/LoadingButton';
import Button from 'tui/components/buttons/Button';
import DropdownItem from 'tui/components/dropdown/DropdownItem';
import Dropdown from 'tui/components/dropdown/Dropdown';
import { notify } from 'tui/notifications';

// GraphQL queries
import joinWorkspace from 'container_workspace/graphql/join_workspace';
import requestToJoin from 'container_workspace/graphql/request_to_join';
import leaveWorkspace from 'container_workspace/graphql/leave_workspace';

export default {
  components: {
    Button,
    LoadingButton,
    DropdownItem,
    Dropdown,
  },

  props: {
    workspaceId: {
      type: [String, Number],
      required: true,
    },

    title: {
      type: String,
      required: true,
    },

    image: {
      type: String,
      required: true,
    },

    url: {
      type: String,
      required: true,
    },

    joined: Boolean,

    /**
     * Whether the actor is able to join the workspace or. For now, every other user is able to join
     * the workspace.
     */
    joinAble: Boolean,

    /**
     * Whether the actor is able to request to join the workspace or not.
     */
    requestToJoinAble: Boolean,

    /**
     * Whether the actor has already requested to join or not.
     */
    hasRequestedToJoin: Boolean,

    /**
     * Whether the actor is an owner of this workspace.
     */
    owned: Boolean,
  },

  data() {
    return {
      submitting: false,
    };
  },

  watch: {
    joined(value) {
      this.$nextTick(() => {
        if (value) {
          this.$refs.joinedButtonRef.$el.focus();
        } else {
          this.$refs.joinButtonRef.$el.focus();
        }
      });
    },
  },

  methods: {
    handleClick(href) {
      window.location.href = href;
    },

    async joinWorkspace() {
      if (this.submitting) {
        return;
      }

      this.submitting = true;

      try {
        const {
          data: { member },
        } = await this.$apollo.mutate({
          mutation: joinWorkspace,
          refetchAll: false,
          variables: {
            workspace_id: this.workspaceId,
          },
        });

        this.$emit('join-workspace', member);
      } catch (e) {
        await notify({
          message: this.$str('error:join_space', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.submitting = false;
      }
    },

    async leaveWorkspace() {
      if (this.submitting) {
        return;
      }

      this.submitting = true;
      try {
        const {
          data: { member },
        } = await this.$apollo.mutate({
          mutation: leaveWorkspace,
          variables: {
            workspace_id: this.workspaceId,
          },
        });

        this.$emit('leave-workspace', member);
      } catch (e) {
        await notify({
          message: this.$str('error:leave_space', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.submitting = false;
      }
    },

    async requestToJoinWorkspace() {
      if (this.submitting) {
        return;
      }

      this.submitting = true;
      try {
        const {
          data: { member_request },
        } = await this.$apollo.mutate({
          mutation: requestToJoin,
          refetchAll: false,
          variables: {
            workspace_id: this.workspaceId,
          },
        });

        this.$emit('request-to-join-workspace', member_request);
      } catch (e) {
        await notify({
          message: this.$str('error:request_to_join', 'container_workspace'),
          type: 'error',
        });
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "join_space",
      "join_me",
      "joined",
      "request_to_join",
      "request_to_join_space",
      "requested_to_join",
      "leave_workspace",
      "error:join_space",
      "error:leave_space",
      "error:request_to_join"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-originalSpaceCard {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: var(--totara-engage-card-height);
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  border: var(--border-width-thin) solid var(--color-neutral-5);
  border-radius: var(--border-radius-normal);
  transition: box-shadow var(--transition-form-function)
    var(--transition-form-duration);

  &:hover {
    cursor: pointer;
  }

  &:hover,
  &:focus {
    box-shadow: var(--shadow-2);
  }

  &:focus-within,
  &.tui-focusWithin {
    box-shadow: var(--shadow-2);
  }

  &__titleBox {
    display: flex;
    flex-basis: 50%;
    flex-direction: column;
    width: 100%;
    padding: var(--gap-4) var(--gap-2);
    padding-bottom: 0;
    background-image: linear-gradient(
      to top,
      transparent 0%,
      var(--color-backdrop-heavy) 78%,
      var(--color-backdrop-heavy)
    );

    border-top-left-radius: var(--border-radius-normal);
    border-top-right-radius: var(--border-radius-normal);
  }

  &__title {
    @include tui-font-heading-x-small();
    margin: 0;
  }

  &__link {
    color: var(--color-neutral-1);
    &:hover,
    &:focus {
      color: var(--color-neutral-1);
      text-decoration: none;
      outline: none;
    }
  }

  &__actions {
    display: flex;
    flex-basis: 50%;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    width: 100%;

    &-button,
    &-dropDown {
      margin-bottom: var(--gap-4);
    }
  }
}
</style>
