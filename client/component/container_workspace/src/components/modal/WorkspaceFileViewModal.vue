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

  @author Qingyang Liu <qingyang liu@totaralearning.com>
  @module container_workspace
-->
<template>
  <Modal
    :dismissable="{
      overlayClose: false,
      esc: true,
      backdropClick: true,
    }"
    class="tui-workspaceFileViewModal"
  >
    <ModalContent
      class="tui-workspaceFileViewModal__content"
      :close-button="true"
    >
      <div class="tui-workspaceFileViewModal__header">
        <p class="tui-workspaceFileViewModal__title">{{ fileName }}</p>
        <div class="tui-workspaceFileViewModal__links">
          <a :href="contextUrl" class="tui-workspaceFileViewModal__discussion">
            {{ $str('view_discussion', 'container_workspace') }}
          </a>
          <a :href="downloadUrl" class="tui-workspaceFileViewModal__link">
            {{ $str('download', 'moodle') }}
          </a>
        </div>
      </div>

      <template v-if="fileType === 'IMAGE'">
        <img
          :src="fileUrl"
          :alt="altText"
          class="tui-workspaceFileViewModal__media"
        />
      </template>
      <template v-else-if="fileType === 'VIDEO'">
        <CoreVideoBlock
          :url="fileUrl"
          :filename="fileName"
          :mimetype="mimeType"
          class="tui-workspaceFileViewModal__media"
        />
      </template>
      <template v-else-if="fileType === 'AUDIO'">
        <audio controls class="tui-workspaceFileViewModal__audio">
          <source :src="fileUrl" :type="mimeType" />
        </audio>
      </template>
      <div v-else class="tui-workspaceFileViewModal__area">
        <div class="tui-workspaceFileViewModal__area__icon">
          <FileIcon :filename="fileName" />
        </div>
        <p>
          {{ $str('file_preview_empty', 'container_workspace') }}
        </p>
      </div>
    </ModalContent>
  </Modal>
</template>

<script>
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import FileIcon from 'tui/components/icons/files/compute/FileIcon';
import CoreVideoBlock from 'tui/components/json_editor/nodes/VideoBlock';

export default {
  components: {
    Modal,
    ModalContent,
    FileIcon,
    CoreVideoBlock,
  },

  props: {
    fileName: {
      type: String,
      required: true,
    },

    downloadUrl: {
      type: String,
      required: true,
    },

    contextUrl: {
      type: String,
      required: true,
    },

    fileUrl: {
      type: String,
      required: true,
    },

    extension: {
      type: String,
      required: true,
    },

    mimeType: {
      type: String,
      required: true,
    },

    fileType: {
      type: String,
      required: true,
    },

    altText: {
      type: String,
      required: true,
    },
  },
};
</script>
<lang-strings>
  {
    "container_workspace": [
      "view_discussion",
      "file_preview_empty",
      "file_image",
      "file_video",
      "file_audio"
    ],

    "moodle": [
      "download"
    ]
  }
</lang-strings>
