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
  @module totara_core
-->

<template>
  <div
    :data-attrs="attributes"
    class="tui-attachmentNode"
    :class="{
      'tui-attachmentNode--withDownloadUrl': !!downloadUrl,
    }"
    :tabindex="downloadUrl ? 0 : -1"
    :role="downloadUrl ? 'link' : false"
    @click="downloadFile"
    @keydown.enter="downloadFile"
  >
    <FileIcon
      :filename="filename"
      size="600"
      :custom-class="['tui-attachmentNode__icon']"
      :title="$str('filewithname', 'totara_core', filename)"
    />

    <div class="tui-attachmentNode__info">
      <div
        class="tui-attachmentNode__info__filename"
        :data-file-extension="fileExtension"
        :class="[truncate && `tui-attachmentNode__info__filename--truncate`]"
      >
        <p ref="filenameText">{{ filename }}</p>
      </div>

      <p class="tui-attachmentNode__info__fileSize">
        <FileSize :size="fileSize" />
      </p>
    </div>
  </div>
</template>

<script>
import FileIcon from 'tui/components/icons/files/compute/FileIcon';
import FileSize from 'tui/components/file/FileSize';
import { isRtl } from 'tui/i18n';

export default {
  components: {
    FileIcon,
    FileSize,
  },

  inheritAttrs: false,

  props: {
    fileSize: {
      type: [String, Number],
      required: true,
    },

    filename: {
      type: String,
      required: true,
    },

    downloadUrl: {
      type: String,
      default: null,
    },

    option: {
      type: Object,
      default() {
        return {};
      },
    },
  },

  data() {
    return {
      truncate: true,
    };
  },

  computed: {
    attributes() {
      return JSON.stringify({
        url: this.url,
        filename: this.filename,
        option: this.option,
      });
    },

    fileExtension() {
      const separator = '.';
      if (this.filename.indexOf(separator) === -1) {
        // No dot.
        return null;
      }

      let parts = String.prototype.split.call(this.filename, separator);
      return parts.pop();
    },
  },

  mounted() {
    if (isRtl()) {
      this.truncate = false;
    } else {
      this.$nextTick().then(() => {
        const el = this.$refs.filenameText;
        if (el.offsetWidth >= el.scrollWidth) {
          this.truncate = false;
        }
      });
    }
  },

  methods: {
    downloadFile() {
      if (!this.downloadUrl) {
        return;
      }

      window.document.location.href = this.downloadUrl;
    },
  },
};
</script>
<lang-strings>
  {
    "totara_core": [
      "filewithname"
    ]
  }
</lang-strings>
