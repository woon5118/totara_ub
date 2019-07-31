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
  <div class="tui-commentReplyHeader">
    <ModalPresenter :open="openModal" @request-close="openModal = false">
      <ConfirmDeleteCommentReplyModal
        @confirm-delete="confirmDelete"
        @request-close="openModal = false"
      />
    </ModalPresenter>

    <div
      class="tui-commentReplyHeader__content"
      :class="{
        'tui-commentReplyHeader__content--inline': inlineHead,
      }"
    >
      <CommentUserLink
        :size="size"
        :profile-url="profileUrl"
        :user-full-name="userFullName"
        class="tui-commentReplyHeader__content__link"
        :class="{
          'tui-commentReplyHeader__content__link--inline': inlineHead,
        }"
      />

      <p
        class="tui-commentReplyHeader__content__timeDescription"
        :class="{
          'tui-commentReplyHeader__content__timeDescription--inline': inlineHead,
        }"
      >
        <span>{{ timeDescription }}</span>
        <span v-if="edited">{{ $str('edited', 'totara_comment') }}</span>
      </p>
    </div>

    <CommentActionDropDown
      :show-delete-action="deleteAble"
      :show-update-action="updateAble"
      :show-report-action="reportAble"
      class="tui-commentReplyHeader__menu"
      @click-edit="$emit('click-edit')"
      @click-report-content="$emit('click-report-content')"
      @click-delete="openModal = true"
    />
  </div>
</template>

<script>
import CommentUserLink from 'totara_comment/components/profile/CommentUserLink';
import CommentActionDropDown from 'totara_comment/components/action/CommentActionDropDown';
import { isValid, SIZE_SMALL } from 'totara_comment/size';
import ConfirmDeleteCommentReplyModal from 'totara_comment/components/modal/ConfirmDeleteCommentReplyModal';
import ModalPresenter from 'tui/components/modal/ModalPresenter';

export default {
  components: {
    CommentUserLink,
    CommentActionDropDown,
    ConfirmDeleteCommentReplyModal,
    ModalPresenter,
  },

  props: {
    size: {
      type: String,
      default() {
        return SIZE_SMALL;
      },

      validator(prop) {
        return isValid(prop);
      },
    },

    profileUrl: {
      type: String,
      required: true,
    },

    userFullName: {
      type: String,
      required: true,
    },

    timeDescription: {
      type: String,
      required: true,
    },

    deleteAble: {
      type: Boolean,
      default: false,
    },

    updateAble: {
      type: Boolean,
      default: false,
    },

    reportAble: {
      type: Boolean,
      default: false,
    },

    edited: {
      type: Boolean,
      required: true,
    },

    inlineHead: Boolean,
  },

  data() {
    return {
      openModal: false,
    };
  },

  methods: {
    confirmDelete() {
      // Close the modal
      this.openModal = false;
      this.$emit('confirm-delete');
    },
  },
};
</script>

<lang-strings>
  {
    "totara_comment": [
      "edited"
    ]
  }
</lang-strings>
