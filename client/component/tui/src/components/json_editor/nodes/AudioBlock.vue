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
  <div class="tui-audioBlock" :data-attrs="attributes">
    <audio controls>
      <source :src="url" :type="mimeType" />
      {{ $str('audionotsupported', 'totara_core') }}
    </audio>
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

    filename: {
      type: String,
      required: true,
    },

    url: {
      type: String,
      required: true,
    },
  },

  computed: {
    attributes() {
      return JSON.stringify({
        itemid: this.itemId,
        url: this.url,
        filename: this.filename,
        area: this.area,
        component: this.component,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "audionotsupported"
    ]
  }
</lang-strings>
