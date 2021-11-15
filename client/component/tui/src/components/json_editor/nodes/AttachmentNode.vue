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
    class="tui-attachmentNode"
    :class="{
      'tui-attachmentNode--downloadable': !!downloadUrl,
    }"
    :tabindex="downloadUrl ? 0 : -1"
    :role="downloadUrl ? 'link' : null"
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
        class="tui-attachmentNode__filename"
        :data-file-extension="fileExtension"
        :class="[truncate && `tui-attachmentNode__filename--truncate`]"
      >
        <p ref="filenameText">{{ filename }}</p>
      </div>

      <p class="tui-attachmentNode__fileSize">
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
    /** @deprecated since 13.3 */
    attributes: () => null,

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

<style lang="scss">
.tui-attachmentNode {
  @media (max-width: 490px) {
    // From 490px downward
    width: 100%;
    overflow: hidden;
  }

  @media (min-width: 491px) {
    // From 490px onward
    flex-basis: 20%;
    min-width: 235px;
  }

  position: relative;
  display: flex;
  align-items: center;
  padding: var(--gap-2);
  white-space: normal;
  border: var(--border-width-thin) solid var(--color-neutral-5);
  border-radius: var(--card-border-radius);

  &__info {
    width: calc(100% - 3.2rem - var(--gap-2) - 2.2em);
  }

  &__fileSize {
    margin: 0;
    font-size: var(--font-size-3);
    white-space: nowrap;
  }

  &__filename {
    position: relative;

    > p {
      margin: 0;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    &--truncate {
      &:after {
        position: absolute;
        top: 0;
        left: 100%;
        width: 2.2em;
        content: attr(data-file-extension);
      }
    }
  }

  &__icon {
    flex-shrink: 0;
    width: 3.2rem;
    margin-right: var(--gap-2);
    color: var(--color-state);
  }

  &--downloadable {
    cursor: pointer;
  }
}
</style>
