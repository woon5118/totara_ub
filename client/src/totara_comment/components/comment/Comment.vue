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
  <CommentCard class="tui-comment" :data-id="commentId">
    <Avatar
      slot="profile-picture"
      :image-src="userProfileImageUrl"
      :image-alt="userProfileImageAlt || userFullName"
      :profile-url="profileUrl"
    />

    <CommentHeader
      slot="body-header"
      :size="size"
      :profile-url="profileUrl"
      :user-full-name="userFullName"
      :time-description="timeDescription"
      :update-able="updateAble"
      :delete-able="deleteAble"
      :report-able="reportAble"
      :edited="edited"
      :inline-head="inlineHead"
      @click-edit="showForm.comment = true"
      @confirm-delete="deleteComment"
      @click-report-content="reportComment"
    />

    <CommentContent
      slot="body-content"
      :deleted="deleted"
      :item-id="commentId"
      :content="content"
      :on-edit="showForm.comment"
      :is-reply="false"
      :size="size"
      @cancel-editing="showForm.comment = false"
      @update-item="updateComment"
    />

    <CommentAction
      v-if="canInteract"
      slot="body-footer"
      :total-replies="totalReplies"
      :size="size"
      :comment-id="commentId"
      :total-reactions="totalReactions"
      :reacted="reacted"
      :react-able="reactAble"
      :reply-able="replyAble"
      :show-like-button-text="showLikeButtonText"
      :show-reply-button-text="showReplyButtonText"
      area="comment"
      class="tui-comment__footer"
      @click-reply="handleReply"
      @update-react-status="updateReactStatus"
    />

    <ReplyBox
      ref="reply-box"
      slot="reply-box"
      :reply-able="replyAble"
      :comment-id="commentId"
      :area="area"
      :component="component"
      :show-reply-form="showForm.reply"
      :total-replies="totalReplies"
      :submitting="innerSubmitting"
      :size="size"
      :show-like-button-text="showLikeButtonText"
      :show-reply-button-text="showReplyButtonText"
      :reply-to="replyTo"
      :reply-head-inline="inlineHead"
      class="tui-comment__replyBox"
      @update-show-reply-form="showForm.reply = $event"
      @create-reply="$emit('add-reply', commentId)"
      @update-submitting="$emit('update-submitting', $event)"
      @form-ready="replyForm = $event"
      @update-reply-to="replyTo = $event"
      @scroll-to-reply-form="scrollToReplyForm"
      @fetch-replies="fetchingReplies = $event"
    />
  </CommentCard>
</template>

<script>
import CommentCard from 'totara_comment/components/card/CommentCard';
import Avatar from 'totara_comment/components/profile/CommentAvatar';
import CommentAction from 'totara_comment/components/action/CommentAction';
import { isValid, SIZE_SMALL } from 'totara_comment/size';
import CommentHeader from 'totara_comment/components/card/CommentReplyHeader';
import CommentContent from 'totara_comment/components/content/CommentReplyContent';
import ReplyBox from 'totara_comment/components/box/ReplyBox';
import { notify } from 'tui/notifications';
import pending from 'tui/pending';

// GraphQL queries
import updateComment from 'totara_comment/graphql/update_comment';
import deleteComment from 'totara_comment/graphql/delete_comment';
import createReview from 'totara_reportedcontent/graphql/create_review';

export default {
  components: {
    ReplyBox,
    CommentHeader,
    CommentAction,
    CommentCard,
    Avatar,
    CommentContent,
  },

  props: {
    commentId: {
      required: true,
      type: [String, Number],
    },

    component: {
      type: String,
      required: true,
    },

    area: {
      type: String,
      required: true,
    },

    /**
     * This is the instance's id where the comment is being used.
     */
    instanceId: {
      type: [String, Number],
      required: true,
    },

    submitting: Boolean,

    totalReplies: {
      type: [Number, String],
      required: true,
    },

    totalReactions: [Number, String],
    reacted: Boolean,

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

    replyAble: {
      type: Boolean,
      default: true,
    },

    reactAble: {
      type: Boolean,
      default: true,
    },

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

    showLikeButtonText: Boolean,
    showReplyButtonText: Boolean,
    inlineHead: Boolean,
  },

  data() {
    return {
      showForm: {
        reply: false,
        comment: false,
      },
      // Caching the innerSubmitting.
      innerSubmitting: this.submitting,
      replyTo: null,

      // We need to control the form from the children up here, so that we can scroll to the element
      // as many times as we want and easily.
      replyForm: null,

      // A flag to tell whether the reply box is fetching the replies or not.
      fetchingReplies: false,
    };
  },

  computed: {
    canInteract() {
      return this.replyAble || this.reactAble;
    },

    profileUrl() {
      return this.$url('/user/profile.php', { id: this.userId });
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
    async $_completeFetchingReplies() {
      if (!this.fetchingReplies) {
        // Replies are not fetching, so we can skip this one.
        return;
      }

      let complete = pending('totara_comment_fetching_replies'),
        callbackLoop = resolve => {
          setTimeout(() => {
            if (!this.fetchingReplies) {
              // Righty, not fetching anymore, we can just finish it here.
              complete();
              resolve();
            } else {
              // Otherwise keep going on until it is done.
              callbackLoop(resolve);
            }
          }, 100);
        };

      return new Promise(callbackLoop);
    },

    /**
     * @param {String} content
     * @param {Number} id       => Comment's id
     * @param {Number} format
     * @param {Number} itemId   => File storage draft's id.
     */
    async updateComment({ content, id, format, itemId }) {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;

      try {
        let {
          data: { comment },
        } = await this.$apollo.mutate({
          mutation: updateComment,
          refetchAll: false,
          variables: {
            id: id,
            content: content,
            format: format,
            draft_id: itemId,
          },
        });

        this.$emit('update-comment', comment);
        this.showForm.comment = false;
      } finally {
        this.innerSubmitting = false;
      }
    },

    async deleteComment() {
      if (this.innerSubmitting) {
        return;
      }

      this.innerSubmitting = true;
      try {
        let {
          data: { comment },
        } = await this.$apollo.mutate({
          mutation: deleteComment,
          refetchAll: false,
          variables: {
            id: this.commentId,
          },
        });

        this.$emit('delete-comment', comment);
      } finally {
        this.innerSubmitting = false;
      }
    },

    async reportComment() {
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
      this.$emit('update-react-status', { status: status, id: this.commentId });
    },

    /**
     *
     * This function is only being called in here. Therefore, we will set the showForm to true
     * and reset the replyTo to null if it has value.
     */
    async handleReply() {
      this.showForm.reply = true;
      if (null !== this.replyTo) {
        this.replyTo = null;
      }

      await this.scrollToReplyForm();
    },

    /**
     * Making the browser to scroll to this reply form. However, we need to make sure that the reply box has
     * finished rendering with updated data so that the form can be scroll easily.
     */
    async scrollToReplyForm() {
      let replyBox = this.$refs['reply-box'];
      if (!replyBox) {
        return;
      }

      await replyBox.$nextTick();
      if (this.fetchingReplies) {
        // So the reply box is fetching the content. Time to check if the loading is complete then start checking
        // the rendering state and eventually we are able to scroll.
        await this.$_completeFetchingReplies();
        await replyBox.$nextTick();
      }

      if (this.replyForm) {
        this.replyForm.scrollIntoView(false);
      }
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
