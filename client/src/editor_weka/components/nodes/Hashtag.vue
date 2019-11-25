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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module editor_weka
-->

<template>
  <Hashtag
    :url="url"
    :text="$str('hashtag', 'editor_weka', text)"
    @click.native.prevent
  />
</template>

<script>
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import Hashtag from 'tui/components/json_editor/nodes/Hashtag';

export default {
  components: {
    Hashtag,
  },

  extends: BaseNode,

  computed: {
    text() {
      return this.attrs.text;
    },

    url() {
      return this.$url('/totara/catalog/index.php', {
        catalog_fts: this.attrs.text,
      });
    },
  },
};
</script>

<lang-strings>
  {
    "editor_weka": [
      "hashtag"
    ]
  }
</lang-strings>
