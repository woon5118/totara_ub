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
  <div class="tui-linkBlock">
    <div v-if="attrs.image" class="tui-linkBlock__image" :style="imageStyle" />
    <div class="tui-linkBlock__summary">
      <template v-if="hasInfo">
        <div class="tui-linkBlock__site">
          {{ domain }}
        </div>
        <div class="tui-linkBlock__title">
          {{ attrs.title }}
        </div>
        <div class="tui-linkBlock__description">
          {{ attrs.description }}
        </div>
      </template>
      <div v-else class="tui-linkBlock__linkOnly">
        {{ attrs.url }}
      </div>
    </div>
    <a class="tui-linkBlock__overlayLink" :href="attrs.url">
      <span class="sr-only">{{ attrs.title || attrs.url }}</span>
    </a>
  </div>
</template>

<script>
export default {
  props: {
    attrs: {
      type: Object,
      required: true,
    },
  },

  computed: {
    hasInfo() {
      return !!this.attrs.title;
    },

    domain() {
      const { url } = this.attrs;
      const match = /^https?:\/\/(?:www.)?([^/]+)/.exec(url);
      return match ? match[1] : null;
    },

    imageStyle() {
      return {
        backgroundImage: 'url("' + encodeURI(this.attrs.image) + '")',
      };
    },
  },
};
</script>
