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
  @module totara_comment
-->
<template>
  <ReplyCard :data-id="replyId" class="tui-reply">
    <Avatar
      slot="profile-picture"
      :image-src="userProfileImageUrl"
      :image-alt="userProfileImageAlt"
      :profile-url="profileUrl"
    />

    <ReplyHeader
      slot="body-header"
      :size="size"
      :user-full-name="userFullName"
      :profile-url="profileUrl"
      :time-description="timeDescription"
      :edited="edited"
      :update-able="updateAble"
      :delete-able="deleteAble"
      :report-able="reportAble"
      :inline-head="inlineHead"
      @confirm-delete="deleteReply"
      @click-report-content="reportReply"
      @click-edit="editting = true"
    />

    <ReplyContent
      slot="body-content"
      :item-id="replyId"
      :deleted="deleted"
      :on-edit="editting"
      :content="content"
      :is-reply="true"
      :size="size"
      @cancel-editing="editting = false"
      @update-item="updateReply"
    />

    <CommentAction
      v-if="canInteract"
      slot="body-footer"
      :size="size"
      :comment-id="replyId"
      :total-reactions="totalReactions"
      :reacted="reacted"
      :react-able="reactAble"
      :reply-able="replyAble"
      :show-reply-button-text="showReplyButtonText"
      :show-like-button-text="showLikeButtonText"
      area="reply"
      class="tui-reply__footer"
      @click-reply="handleReply"
      @update-react-status="updateReactStatus"
    />
  </ReplyCard>
</template>

<script>
import Avatar from 'totara_comment/components/profile/CommentAvatar';
import ReplyCard from 'totara_comment/components/card/ReplyCard';
import CommentAction from 'totara_comment/components/action/CommentAction';
import { isValid, SIZE_SMALL } from 'totara_comment/size';
import ReplyHeader from 'totara_comment/components/card/CommentReplyHeader';
import ReplyContent from 'totara_comment/components/content/CommentReplyContent';
import { notify } from 'tui/notifications';

// GraphQL queries
import updateReply from 'totara_comment/graphql/update_reply';
import deleteReply from 'totara_comment/graphql/delete_reply';
import createReview from 'totara_reportedcontent/graphql/create_review';

export default {
  components: {
    ReplyHeader,
    CommentAction,
    ReplyCard,
    Avatar,
    ReplyContent,
  },

  props: {
    replyId: {
      type: [Number, String],
      required: true,
    },

    commentId: {
      type: [String, Number],
      required: true,
    },

    submitting: Boolean,

    userFullName: {
      type: String,
      required: true,
    },

    userId: {
      type: [String, Number],
      required: true,
    },

    userProfileImageUrl: {
      type: String,
      required: true,
    },

    userProfileImageAlt: {
      type: String,
      default: '',
    },

    timeDescription: {
      type: String,
      required: true,
    },

    reportAble: {
      type: Boolean,
      required: true,
    },

    content: {
      type: String,
      required: true,
    },

    deleteAble: {
      type: Boolean,
      required: true,
    },

    updateAble: {
      type: Boolean,
      required: true,
    },

    size: {
      type: String,
      default() {
        return SIZE_SMALL;
      },

      validator(prop) {
        return isValid(prop);
      },
    },

    edited: {
      type: Boolean,
      required: true,
    },

    deleted: {
      type: Boolean,
      required: true,
    },

    totalReactions: [Number, String],
    reacted: Boolean,
    replyAble: {
      type: Boolean,
      default: true,
    },
    reactAble: {
      type: Boolean,
      default: true,
    },

    showLikeButtonText: Boolean,
    showReplyButtonText: Boolean,
    inlineHead: Boolean,
  },

  data() {
    return {
      editting: false,
      innerSubmitting: this.submitting,
    };
  },

  computed: {
    profileUrl() {
      return this.$url('/user/profile.php', { id: this.userId });
    },

    canInteract() {
      return this.replyAble || this.reactAble;
    },
  },

  watch: {
    /**
     * @param {Boolean} value
     */
    innerSubmitting(value) {
      if (value === this.submitting) {
        return;
      }

      this.$emit('update-submitting', value);
    },

    /**
     * @param {Boolean} value
     */
    submitting(value) {
      this.innerSubmitting = value;
    },
  },

  methods: {
    handleReply() {
      const params = {
        fullname: this.userFullName,
        userId: this.userId,
      };

      this.$emit('reply-to', params);
    },

    /**
     *
     * @param {Number} id
     * @param {String} content
     * @param {Number} format
     * @param {Number} itemId   => The file storage item's id.
     *
     * @return {Promise<void>}
     */
    async updateReply({ id, content, format, itemId }) {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        let {
          data: { reply },
        } = await this.$apollo.mutate({
          mutation: updateReply,
          refetchAll: false,
          variables: {
            id: id,
            content: content,
            format: format,
            draft_id: itemId,
          },
        });

        this.editting = false;
        this.$emit('update-reply', reply);
      } finally {
        this.innerSubmitting = false;
      }
    },

    /**
     *
     * @return {Promise<void>}
     */
    async deleteReply() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        let {
          data: { reply },
        } = await this.$apollo.mutate({
          mutation: deleteReply,
          refetchAll: false,
          variables: {
            id: this.replyId,
          },
        });

        this.$emit('delete-reply', reply);
      } finally {
        this.innerSubmitting = false;
      }
    },

    async reportReply() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        await this.$apollo.mutate({
          mutation: createReview,
          refetchAll: false,
          variables: {
            component: this.component,
            area: this.area,
            item_id: this.commentId,
            instance_id: this.instanceId,
            url: window.location.href,
          },
        });

        await notify({
          duration: 2000,
          message: this.$str('reported', 'totara_reportedcontent'),
          type: 'success',
        });
      } catch (e) {
        await notify({
          duration: 2000,
          message: this.$str('reported_failed', 'totara_reportedcontent'),
          type: 'error',
        });
      } finally {
        this.innerSubmitting = false;
      }
    },

    /**
     *
     * @param {Boolean} status
     */
    updateReactStatus(status) {
      this.$emit('update-react-status', {
        id: this.replyId,
        status: status,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_reportedcontent": [
      "reported",
      "reported_failed"
    ]
  }
</lang-strings>
