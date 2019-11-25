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
  <div class="tui-videoBlock" :data-attrs="attributes">
    <video ref="videojs" class="video-js">
      <source :src="url" :type="mimeType" />
    </video>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,

  props: {
    mimeType: {
      type: String,
      required: true,
    },

    url: {
      type: String,
      required: true,
    },

    filename: {
      type: String,
      required: true,
    },
  },

  computed: {
    attributes() {
      return JSON.stringify({
        mime_type: this.mimeType,
        filename: this.filename,
        url: this.url,
      });
    },

    config() {
      return {
        controls: true,
        controlBar: {
          fullscreenToggle: true,
        },
      };
    },
  },

  async mounted() {
    await this.$nextTick();
    if (!this.$refs.videojs) {
      return;
    }

    const videojs = tui.defaultExport(await tui.import('ext_videojs/videojs'));
    if (this.isDestroyed) {
      return;
    }
    this.player = videojs(this.$refs.videojs, this.config);
  },

  beforeDestroy() {
    this.isDestroyed = true;
    if (this.player) {
      this.player.dispose();
      this.player = null;
    }
  },
};
</script>
