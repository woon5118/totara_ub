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
  @module editor_weka
-->

<template>
  <!-- A component for displaying a draft file as attachment -->
  <div class="tui-editorWeka-imageAttachment">
    <AttachmentNode
      :filename="filename"
      :file-size="fileSize"
      component="user"
      area="draft"
      :item-id="draftId"
    />

    <NodeBar
      :actions="actions"
      :aria-label="$str('actions_menu_for', 'editor_weka', filename)"
    />
  </div>
</template>

<script>
import AttachmentNode from 'tui/components/json_editor/nodes/AttachmentNode';
import NodeBar from 'editor_weka/components/toolbar/NodeBar';
import AttachmentMixin from 'editor_weka/mixins/attachment_mixin';

export default {
  components: {
    AttachmentNode,
    NodeBar,
  },

  mixins: [AttachmentMixin],

  props: {
    enableConvert: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      showModal: false,
    };
  },

  computed: {
    actions() {
      let rtn = [];

      if (this.enableConvert) {
        rtn.push({
          label: this.$str('display_as_embedded_media', 'editor_weka'),
          action: () => {
            this.$emit('convert-to-embedded-media');
          },
        });
      }

      rtn = rtn.concat([
        {
          label: this.$str('remove', 'moodle'),
          action: () => {
            this.$emit('delete');
          },
        },
      ]);

      if (this.hasDownloadUrl) {
        rtn.push({
          label: this.$str('download', 'moodle'),
          action: () => {
            this.$emit('download');
          },
        });
      }

      return rtn;
    },
  },
};
</script>

<lang-strings>
  {
    "editor_weka": [
      "display_as_embedded_media",
      "actions_menu_for"
    ],

    "moodle": [
      "remove",
      "download"
    ]
  }
</lang-strings>
