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
        <ModalPresenter :open="showModal" @request-close="hideModal">
          <ExtraFileUploadModal
            :item-id="itemId"
            :context-id="contextId"
            :accepted-file-types="['.txt']"
            :modal-title="$str('upload_audio_transcript', 'editor_weka')"
            :modal-title-help-text="
              $str('transcript_modal_title_help', 'editor_weka')
            "
            :submit-button-text="
              $str('upload_caption_transcript_button', 'editor_weka', '.txt')
            "
            :modal-help-text="$str('audio_alt_help', 'editor_weka')"
            :filename="transcriptFilename"
            @change="updateTranscript"
          />
        </ModalPresenter>
        <CoreAudioBlock
          :filename="filename"
          :item-id="itemId"
          :url="file.url"
          :mime-type="file.mime_type"
          :transcript-url="transcriptUrl"
        >
          <template slot="actions">
            <NodeBar
              :actions="actions"
              :aria-label="$str('actions_menu_for', 'editor_weka', filename)"
            />
          </template>
        </CoreAudioBlock>
      </div>
    </template>
  </div>
</template>

<script>
import CoreAudioBlock from 'tui/components/json_editor/nodes/AudioBlock';
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import getDraftFile from 'editor_weka/graphql/get_draft_file';
import NodeBar from 'editor_weka/components/toolbar/NodeBar';
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import ExtraFileUploadModal from 'editor_weka/components/upload/ExtraFileUploadModal';

export default {
  components: {
    CoreAudioBlock,
    NodeBar,
    ModalPresenter,
    ExtraFileUploadModal,
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
      showModal: false,
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
          label: this.$str('upload_transcript', 'editor_weka'),
          action: this.$_showModal,
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

    contextId() {
      if (!this.context.getContextId) {
        throw new Error("No function 'getContextId' was found from extension");
      }
      return this.context.getContextId();
    },

    filename() {
      if (!this.attrs.filename) {
        return null;
      }

      return this.attrs.filename;
    },

    transcriptFilename() {
      if (!this.attrs.transcript) {
        return null;
      }

      return this.attrs.transcript.filename;
    },

    transcriptUrl() {
      if (!this.attrs.transcript) {
        return null;
      }

      return this.attrs.transcript.url;
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
      if (!this.context.replaceWithAttachment) {
        return;
      }

      const params = {
        filename: this.filename,
        size: this.file.file_size,
        transcript: this.attrs.transcript || null,
      };

      this.context.replaceWithAttachment(this.getRange, params);
    },

    async $_download() {
      window.open(await this.context.getDownloadUrl(this.filename));
    },

    $_showModal() {
      this.showModal = true;
    },

    hideModal() {
      this.showModal = false;
    },

    /**
     * @param {Object|null} transcriptFile
     */
    async updateTranscript(transcriptFile) {
      this.hideModal();

      if (!this.context.updateAudioWithTranscript) {
        return;
      }

      const audioAttrs = Object.assign({}, this.attrs, { transcript: null });

      if (transcriptFile) {
        audioAttrs.transcript = {
          filename: transcriptFile.filename,
          url: transcriptFile.url,
        };
      }

      this.context.updateAudioWithTranscript(this.getRange, audioAttrs);
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
      "audio_alt_help",
      "display_as_attachment",
      "actions_menu_for",
      "upload_caption_transcript_button",
      "upload_audio_transcript",
      "upload_transcript",
      "transcript_modal_title_help"
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
