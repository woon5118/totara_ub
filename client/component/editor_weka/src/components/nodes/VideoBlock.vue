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
  <div class="tui-wekaVideoBlock">
    <template v-if="!$apollo.loading">
      <div class="tui-wekaVideoBlock__inner">
        <CoreVideoBlock
          :mime-type="file.mime_type"
          :url="file.url"
          :filename="filename"
        />

        <NodeBar
          :actions="actions"
          :aria-label="$str('actions_menu_for', 'editor_weka', filename)"
        />
      </div>
    </template>

    <template v-else>
      <p>
        {{ $str('loadinghelp', 'core') }}
      </p>
    </template>
  </div>
</template>

<script>
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import CoreVideoBlock from 'tui/components/json_editor/nodes/VideoBlock';
import getDraftFile from 'editor_weka/graphql/get_draft_file';
import NodeBar from 'editor_weka/components/toolbar/NodeBar';

export default {
  components: {
    CoreVideoBlock,
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
    hasAttachmentNode() {
      return this.context.hasAttachmentNode();
    },

    actions() {
      return [
        this.hasAttachmentNode && {
          label: this.$str('display_as_attachment', 'editor_weka'),
          action: this.$_toAttachment,
        },
        {
          label: this.$str('remove', 'core'),
          action: this.$_removeNode,
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
  },

  methods: {
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

    $_removeNode() {
      if (!this.context.removeNode) {
        return;
      }

      this.context.removeNode(this.getRange);
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
    "loadinghelp",
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
.tui-wekaVideoBlock {
  margin: var(--gap-8) 0;
  white-space: normal;

  &.ProseMirror-selectednode {
    outline: none;
  }

  &.ProseMirror-selectednode > &__inner > .tui-videoBlock {
    outline: var(--border-width-normal) solid var(--color-secondary);
  }

  &__inner {
    max-width: 100%;

    .tui-videoBlock {
      // Reset margin
      margin: 0;
      white-space: normal;
    }
  }
}
</style>
