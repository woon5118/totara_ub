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
import Loading from 'tui/components/icons/common/Loading';

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
