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
  <div class="tui-wekaAudioBlock">
    <template v-if="!$apollo.loading">
      <div class="tui-wekaAudioBlock__inner">
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
      return [
        this.hasAttachmentNode && {
          label: this.$str('display_as_attachment', 'editor_weka'),
          action: this.$_toAttachment,
        },
        {
          label: this.$str('remove', 'core'),
          action: () => this.$_removeNode,
        },
        {
          label: this.$str('download', 'core'),
          action: this.$_download,
        },
      ].filter(Boolean);
    },

    itemId() {
      return this.context.getItemId();
    },

    filename() {
      if (!this.attrs.filename) {
        return null;
      }

      return this.attrs.filename;
    },

    hasAttachmentNode() {
      return this.context.hasAttachmentNode();
    },
  },

  methods: {
    $_removeNode() {
      return this.context.removeNode(this.getRange);
    },

    $_toAttachment() {
      const params = {
        filename: this.filename,
        size: this.file.file_size,
      };

      this.context.replaceWithAttachment(this.getRange, params);
    },

    async $_download() {
      window.open(await this.context.getDownloadUrl(this.filename));
    },
  },
};
</script>

<lang-strings>
  {
    "core": [
      "remove",
      "download"
    ],

    "editor_weka": [
      "display_as_attachment",
      "actions_menu_for"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-wekaAudioBlock {
  margin: var(--gap-8) 0;
  white-space: normal;

  &.ProseMirror-selectednode {
    outline: none;
  }

  &.ProseMirror-selectednode > &__inner > .tui-audioBlock {
    outline: var(--border-width-normal) solid var(--color-secondary);
  }

  &__inner {
    display: inline-block;

    .tui-audioBlock {
      margin: 0;
      white-space: normal;

      audio:focus {
        // Removing self outlininga
        outline: none;
      }
    }
  }
}
</style>
