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
  <div class="tui-wekaImageBlock">
    <div v-if="!$apollo.loading" class="tui-wekaImageBlock__inner">
      <ModalPresenter :open="showModal" @request-close="$_hideModal">
        <EditImageAltTextModal :value="altText" @change="$_updateAltText" />
      </ModalPresenter>

      <ImageBlock :url="file.url" :filename="filename" :alt-text="altText" />
      <NodeBar
        :actions="actions"
        :aria-label="$str('actions_menu_for', 'editor_weka', filename)"
      />
    </div>

    <template v-else>
      <p class="tui-wekaImageBlock__text">
        {{ $str('loadinghelp', 'core') }}
      </p>
    </template>
  </div>
</template>

<script>
import ImageBlock from 'tui/components/json_editor/nodes/ImageBlock';
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import getDraftFile from 'editor_weka/graphql/get_draft_file';
import NodeBar from 'editor_weka/components/toolbar/NodeBar';
import EditImageAltTextModal from 'editor_weka/components/editing/EditImageAltTextModal';
import ModalPresenter from 'tui/components/modal/ModalPresenter';

export default {
  components: {
    EditImageAltTextModal,
    ModalPresenter,
    ImageBlock,
    NodeBar,
  },

  extends: BaseNode,

  apollo: {
    file: {
      query: getDraftFile,
      variables() {
        return {
          filename: this.filename,
          item_id: this.itemId,
        };
      },
      batch: true,
    },
  },

  data() {
    return {
      file: {},
      showModal: false,
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
          label: this.altTextLabel,
          action: this.$_showModal,
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

    altTextLabel() {
      if (!this.altText) {
        return this.$str('add_image_alt_text', 'editor_weka');
      }

      return this.$str('edit_image_alt_text', 'editor_weka');
    },

    altText() {
      if (!this.attrs.alttext) {
        return '';
      }

      return this.attrs.alttext;
    },

    filename() {
      if (!this.attrs.filename) {
        return null;
      }

      return this.attrs.filename;
    },

    itemId() {
      return this.context.getItemId();
    },
  },

  methods: {
    $_toAttachment() {
      if (!this.context.replaceWithAttachment) {
        // Error should be thrown here
        return;
      }

      const params = {
        filename: this.filename,
        alttext: this.altText,
        size: this.file.file_size,
      };

      this.context.replaceWithAttachment(this.getRange, params);
    },

    $_showModal() {
      this.showModal = true;
    },

    $_hideModal() {
      this.showModal = false;
    },

    /**
     *
     * @param {String} newValue
     */
    $_updateAltText(newValue) {
      this.$_hideModal();

      const params = {
        filename: this.filename,
        alttext: newValue,
      };

      this.context.updateImage(this.getRange, params);
    },

    $_removeNode() {
      return this.context.removeNode(this.getRange);
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
      "add_image_alt_text",
      "edit_image_alt_text",
      "actions_menu_for"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-wekaImageBlock {
  display: flex;
  min-width: 250px;
  margin: var(--gap-8) 0;
  white-space: normal;

  &.ProseMirror-selectednode {
    outline: none;
  }

  &.ProseMirror-selectednode > &__inner > .tui-imageBlock {
    // Set the outline for the picture only.
    outline: var(--border-width-normal) solid var(--color-secondary);
  }

  &__inner {
    display: inline-block;
    max-width: 100%;
    white-space: normal;

    .tui-imageBlock {
      margin: 0;
    }
  }
}
</style>
