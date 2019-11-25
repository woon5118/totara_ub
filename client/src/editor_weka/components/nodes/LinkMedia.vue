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
  @module editor_weka
-->

<template>
  <div
    class="tui-editorWeka-linkMedia"
    :class="{
      'tui-editorWeka-linkMedia--intrinsic-width': !attrs.loading && !iframeUrl,
    }"
    :data-url="attrs.url"
  >
    <div class="tui-editorWeka-linkMedia__inner">
      <div v-if="attrs.loading">
        Loading...
      </div>
      <div v-else-if="iframeUrl" class="tui-editorWeka-linkMedia__embed">
        <ResponsiveEmbedIframe
          :src="iframeUrl"
          :resolution="attrs.resolution"
          :title="attrs.title || attrs.url"
        />
      </div>
      <div v-else-if="pluginKey == 'audio'">
        <audio controls :src="attrs.url" />
      </div>
      <div v-else-if="pluginKey == 'image'">
        <ResponsiveImage :src="attrs.url" />
      </div>
      <div v-else>
        <a :href="attrs.url">{{ attrs.url }}</a>
      </div>
      <NodeBar
        :actions="actions"
        :aria-label="$str('actions_menu_for', 'editor_weka', summaryText)"
      />
    </div>
  </div>
</template>

<script>
import BaseNode from 'editor_weka/components/nodes/BaseNode';
import NodeBar from 'editor_weka/components/toolbar/NodeBar';
import ResponsiveEmbedIframe from 'tui/components/embeds/ResponsiveEmbedIframe';
import ResponsiveImage from 'tui/components/images/ResponsiveImage';

export default {
  components: {
    NodeBar,
    ResponsiveEmbedIframe,
    ResponsiveImage,
  },

  extends: BaseNode,

  computed: {
    pluginMatch() {
      return (
        this.context.urlPlugin({ type: 'media', url: this.attrs.url }) || {}
      );
    },

    pluginKey() {
      return this.pluginMatch.plugin && this.pluginMatch.plugin.key;
    },

    pluginName() {
      return this.pluginMatch.plugin && this.pluginMatch.plugin.name;
    },

    details() {
      return this.pluginMatch.details || {};
    },

    actions() {
      return [
        {
          label: this.$str('go_to_link', 'editor_weka'),
          action: () => this.open(),
        },
        { label: this.$str('edit', 'moodle'), action: () => this.edit() },
        {
          label: this.$str('display_as_text', 'editor_weka'),
          action: () => this.toLink(),
        },
        {
          label: this.$str('remove', 'moodle'),
          action: () => this.$emit('remove'),
        },
      ];
    },

    iframeUrl() {
      const { details } = this;
      switch (this.pluginKey) {
        case 'youtube':
          return 'https://www.youtube.com/embed/' + details.id + '?rel=0';
        case 'vimeo':
          return 'https://player.vimeo.com/video/' + details.id + '?portrait=0';
      }
      return null;
    },

    summaryText() {
      return this.pluginName + ' - ' + (this.attrs.title || this.attrs.url);
    },
  },

  methods: {
    open() {
      window.open(this.attrs.url);
    },

    edit() {
      this.context.editCard(this.getRange);
    },

    toLink() {
      const url = this.attrs.url;
      this.context.replaceWithTextLink(this.getRange, { url });
    },
  },
};
</script>

<lang-strings>
{
  "editor_weka": ["display_as_text", "go_to_link", "actions_menu_for"],
  "moodle": ["edit", "remove"]
}
</lang-strings>
