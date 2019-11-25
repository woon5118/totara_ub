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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <div class="tui-responsiveEmbedIframe" :style="style">
    <iframe
      class="tui-responsiveEmbedIframe__item"
      :src="src"
      frameborder="0"
      allow="autoplay; encrypted-media"
      allowfullscreen
      :title="title"
    />
  </div>
</template>

<script>
export default {
  props: {
    src: String,
    title: String,
    resolution: Object,
    aspectRatio: Number,
  },

  computed: {
    style() {
      let ratio = this.aspectRatio;

      const res = this.resolution;
      if (!ratio && res) {
        ratio = res.width / res.height;
      }

      if (!ratio) {
        ratio = 16 / 9;
      }

      return {
        paddingTop: (1 / ratio) * 100 + '%',
      };
    },
  },
};
</script>
