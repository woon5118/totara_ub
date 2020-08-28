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
  <div class="tui-editorWeka-imageBlock">
    <div v-if="!$apollo.loading" class="tui-editorWeka-imageBlock__inner">
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
      <p class="tui-editorWeka-imageBlock__text">
        {{ $str('loadinghelp', 'moodle') }}
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
      if (!this.context.hasAttachmentNode) {
        return false;
      }

      return this.context.hasAttachmentNode();
    },

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
          label: this.altTextLabel,
          action: () => {
            this.$_showModal();
          },
        },
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
      if (!this.context.getItemId) {
        throw new Error("No function 'getItemId' for extension media");
      }

      return this.context.getItemId();
    },

    downloadUrl() {
      if (!this.context.getFileUrl) {
        return null;
      }

      return this.context.getFileUrl(this.filename);
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

      if (!this.context.updateImage) {
        return;
      }

      const params = {
        filename: this.filename,
        alttext: newValue,
        mimeType: this.file.mime_type,
      };

      this.context.updateImage(this.getRange, params);
    },

    $_removeNode() {
      if (!this.context.removeNode) {
        return;
      }

      return this.context.removeNode(this.getRange);
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
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
