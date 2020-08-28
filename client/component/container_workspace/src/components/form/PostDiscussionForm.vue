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

  @author Alvin Smith <alvin.smith@totaralearning.com>
  @module container_workspace
-->

<template>
  <div class="tui-postDiscussionForm">
    <PostAvatar
      :profile-url="avatarImageUrl"
      :image-alt="avatarImageAlt"
      :image-src="avatarImageSrc"
    />

    <WorkspaceDiscussionForm
      :submitting="submitting"
      :show-cancel-button="false"
      class="tui-postDiscussionForm__form"
      @submit="$emit('submit', $event)"
    />
  </div>
</template>

<script>
import PostAvatar from 'container_workspace/components/profile/PostAvatar';
import WorkspaceDiscussionForm from 'container_workspace/components/form/WorkspaceDiscussionForm';

export default {
  components: {
    PostAvatar,
    WorkspaceDiscussionForm,
  },

  props: {
    avatarImageUrl: {
      type: String,
      required: true,
    },

    avatarImageAlt: {
      type: String,
      required: true,
    },

    avatarImageSrc: {
      type: String,
      required: true,
    },

    submitting: Boolean,
  },
};
</script>

<style lang="scss">
.tui-postDiscussionForm {
  display: flex;
  &__form {
    flex-grow: 1;
    width: 100%;
    padding-left: var(--gap-2);
  }
}
</style>
