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
  <span class="tui-totaraCore-fileSize">
    {{ fileSize }}
  </span>
</template>

<script>
export default {
  props: {
    size: {
      type: [String, Number],
      default: 0,
      validator(prop) {
        prop = parseInt(prop);
        return !isNaN(prop);
      },
    },
  },

  computed: {
    fileSize() {
      let size = parseInt(this.size);

      if (size === -1) {
        return this.$str('unlimited');
      }

      let params = {
        size: 0,
        unit: '',
      };

      if (size >= 1073741824) {
        params.size = Math.round((size / 1073741824) * 10) / 10;
        params.unit = this.$str('sizegb');
      } else if (size >= 1048576) {
        params.size = Math.round((size / 1048576) * 10) / 10;
        params.unit = this.$str('sizemb');
      } else if (size >= 1024) {
        params.size = Math.round((size / 1024) * 10) / 10;
        params.unit = this.$str('sizekb');
      } else {
        params.size = size;
        params.unit = this.$str('sizeb');
      }

      return this.$str('filesize', 'totara_core', params);
    },
  },
};
</script>

<lang-strings>
  {
    "totara_core": [
      "filesize"
    ],
    "moodle": [
      "unlimited",
      "sizegb",
      "sizemb",
      "sizekb",
      "sizeb"
    ]
  }
</lang-strings>
