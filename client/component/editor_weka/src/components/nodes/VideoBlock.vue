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
        <ModalPresenter :open="showModal" @request-close="hideModal">
          <ExtraFileUploadModal
            :item-id="itemId"
            :context-id="contextId"
            :accepted-file-types="['.vtt']"
            :modal-title="$str('upload_video_caption', 'editor_weka')"
            :submit-button-text="
              $str('upload_caption_transcript_button', 'editor_weka', '.vtt')
            "
            :modal-help-text="$str('video_alt_help', 'editor_weka')"
            :filename="subtitleFilename"
            @change="updateSubtitle"
          />
        </ModalPresenter>

        <CoreVideoBlock
          :key="coreVideoBlockKey"
          :mime-type="file.mime_type"
          :url="file.url"
          :filename="filename"
          :subtitle-url="subtitleUrl"
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
import ModalPresenter from 'tui/components/modal/ModalPresenter';
import ExtraFileUploadModal from 'editor_weka/components/upload/ExtraFileUploadModal';
import { notify } from 'tui/notifications';

export default {
  components: {
    CoreVideoBlock,
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
    hasAttachmentNode() {
      return this.context.hasAttachmentNode();
    },

    /**
     * This text is to help enforcing the video block that contains videojs to rerender
     * when the subtitle is uploaded.
     *
     * @return {String}
     */
    coreVideoBlockKey() {
      if (!this.attrs.subtitle) {
        return this.attrs.filename;
      }

      const {
        filename,
        subtitle: { filename: subtitleFilename, url: subtitleUrl },
      } = this.attrs;
      return `${filename}-${subtitleFilename}-${subtitleUrl}`;
    },

    actions() {
      return [
        this.hasAttachmentNode && {
          label: this.$str('display_as_attachment', 'editor_weka'),
          action: this.$_toAttachment,
        },
        {
          label: this.$str('upload_captions', 'editor_weka'),
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

    /**
     * Getting the subtitle filename.
     * @return {String|?}
     */
    subtitleFilename() {
      if (!this.attrs.subtitle) {
        return null;
      }

      return this.attrs.subtitle.filename;
    },

    subtitleUrl() {
      if (!this.attrs.subtitle) {
        return null;
      }

      return this.attrs.subtitle.url;
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
        subtitle: this.attrs.subtitle || null,
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

    $_showModal() {
      this.showModal = true;
    },

    hideModal() {
      this.showModal = false;
    },

    /**
     *
     * @param {Object|null} subtitleFile
     */
    async updateSubtitle(subtitleFile) {
      this.hideModal();

      if (!this.context.updateVideoWithSubtitle) {
        return;
      }

      let videoAttrs = Object.assign({}, this.attrs),
        oldSubtitleFilename = null,
        changeSubtitle = false;

      if (videoAttrs.subtitle) {
        oldSubtitleFilename = videoAttrs.subtitle.filename;
      }

      videoAttrs.subtitle = null;

      if (subtitleFile) {
        changeSubtitle = oldSubtitleFilename !== subtitleFile.filename;

        videoAttrs.subtitle = {
          filename: subtitleFile.filename,
          url: subtitleFile.url,
        };
      }

      this.context.updateVideoWithSubtitle(this.getRange, videoAttrs);

      if (changeSubtitle) {
        await notify({
          message: this.$str('upload_caption_confirmation', 'editor_weka'),
          type: 'success',
        });
      }
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
    "actions_menu_for",
    "upload_captions",
    "video_alt_help",
    "upload_caption_transcript_button",
    "upload_video_caption",
    "upload_caption_confirmation"
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
