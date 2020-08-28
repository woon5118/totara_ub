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
  <!-- Vue component for displaying audio file as draft -->
  <div class="tui-editorWeka-audioBlock">
    <template v-if="!$apollo.loading">
      <div class="tui-editorWeka-audioBlock__inner">
        <CoreAudioBlock
          :filename="filename"
          :item-id="itemId"
          :url="file.url"
          :mime-type="file.mime_type"
        />

        <NodeBar
          :actions="actions"
          :aria-label="$str('actions_menu_for', 'editor_weka', filename)"
        />
      </div>
    </template>
  </div>
</template>

<script>
import CoreAudioBlock from 'tui/components/json_editor/nodes/AudioBlock';
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import getDraftFile from 'editor_weka/graphql/get_draft_file';
import NodeBar from 'editor_weka/components/toolbar/NodeBar';

export default {
  components: {
    CoreAudioBlock,
    NodeBar,
  },

  extends: BaseNode,
  apollo: {
    file: {
      query: getDraftFile,
      variables() {
        return {
          item_id: this.itemId,
          filename: this.filename,
        };
      },
    },
  },

  data() {
    return {
      file: {},
    };
  },

  computed: {
    actions() {
      let rtn = [];

      if (this.hasAttachmentNode) {
        rtn.push({
          label: this.$str('display_as_attachment', 'editor_weka'),
          action: () => {
            this.$_toAttachment();
          },
        });
      }

      rtn = rtn.concat([
        {
          label: this.$str('remove', 'moodle'),
          action: () => {
            this.$_removeNode();
          },
        },
      ]);

      if (this.downloadUrl) {
        rtn.push({
          label: this.$str('download', 'moodle'),
          action: () => {
            window.document.location.href = this.downloadUrl;
          },
        });
      }

      return rtn;
    },

    itemId() {
      if (!this.context.getItemId) {
        throw new Error('No function "getItemId" found for extension media');
      }

      return this.context.getItemId();
    },

    filename() {
      if (!this.attrs.filename) {
        return null;
      }

      return this.attrs.filename;
    },

    hasAttachmentNode() {
      if (!this.context.hasAttachmentNode) {
        return false;
      }

      return this.context.hasAttachmentNode();
    },

    downloadUrl() {
      if (!this.context.getFileUrl) {
        return null;
      }

      return this.context.getFileUrl(this.filename);
    },
  },

  methods: {
    $_removeNode() {
      if (!this.context.removeNode) {
        return;
      }

      return this.context.removeNode(this.getRange);
    },

    $_toAttachment() {
      if (!this.context.replaceWithAttachment) {
        return;
      }

      const params = {
        filename: this.filename,
        size: this.file.file_size,
      };

      this.context.replaceWithAttachment(this.getRange, params);
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "remove",
      "download"
    ],

    "editor_weka": [
      "display_as_attachment",
      "actions_menu_for"
    ]
  }
</lang-strings>
