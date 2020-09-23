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
  <div class="tui-sidePanelCommentBox">
    <h4 class="tui-sidePanelCommentBox__header">
      <span>{{ $str('comments', 'totara_comment', totalComments) }}</span>
      <Loading v-if="submitting" />
    </h4>

    <CommentBox
      :instance-id="instanceId"
      :component="component"
      :area="area"
      :size="size"
      :submitting="submitting"
      class="tui-sidePanelCommentBox__box"
      @update-total-comments="totalComments = $event"
      @update-submitting="submitting = $event"
    />
  </div>
</template>

<script>
import CommentBox from 'totara_comment/components/box/CommentBox';
import { SIZE_SMALL } from 'totara_comment/size';
import Loading from 'tui/components/icons/Loading';

export default {
  components: {
    CommentBox,
    Loading,
  },

  props: {
    component: {
      type: String,
      required: true,
    },

    area: {
      type: String,
      required: true,
    },

    instanceId: {
      type: [String, Number],
      required: true,
    },
  },

  data() {
    return {
      size: SIZE_SMALL,
      totalComments: 0,
      submitting: false,
    };
  },
};
</script>

<lang-strings>
  {
    "totara_comment": [
      "comments"
    ]
  }
</lang-strings>

<style lang="scss">
:root {
  --totaraComment-sidePanel-commentBox-height: 40vh;
}

.tui-sidePanelCommentBox {
  // Overriding the comment box to make it fit with the sidepanel.
  &__header {
    margin: 0;
    margin-bottom: var(--gap-4);
    padding: 0;
    padding-bottom: var(--gap-2);
    font-size: var(--font-size-14);
    border-bottom: var(--border-width-thin) solid var(--color-primary);
  }

  &__box {
    .tui-commentBox {
      // Overriding the comment thread to make it fit with the sidepanel
      &__comments {
        // This is needed for scrolling
        position: relative;

        height: var(--totaraComment-sidePanel-commentBox-height);
        overflow: auto;

        // This is not support by IE or Edge.
        scroll-behavior: smooth;

        .tui-commentThread {
          &__comment {
            &:not(:first-child) {
              padding-top: var(--gap-4);
              border-top: var(--border-width-thin) solid var(--color-neutral-5);
            }
          }
        }
      }

      &__commentForm {
        padding-top: var(--gap-4);
        border-top: var(--border-width-thin) solid var(--color-primary);
      }
    }
  }
}
</style>
