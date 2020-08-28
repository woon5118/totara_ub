<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Simon Chester <simon.chester@totaralearning.com>
  @module samples
-->

<template>
  <div class="tui-sample-weka">
    <Weka
      v-if="draftId"
      component="editor_weka"
      area="default"
      :doc="doc"
      :file-item-id="draftId"
      @update="handleUpdate"
    />
    <hr />
    <Button text="Reset" @click="reset" />
    <br />
    <div class="tui-sample-weka__json" v-text="json" />
  </div>
</template>

<script>
import Weka from 'editor_weka/components/Weka';
import Button from 'tui/components/buttons/Button';

// GraphQL queries
import getFileUnusedDraftId from 'core/graphql/file_unused_draft_item_id';

export default {
  components: {
    Weka,
    Button,
  },

  data() {
    return {
      doc: null,
      json: '',
      draftId: null,
    };
  },

  created() {
    this.defaultDoc = {
      type: 'doc',
      content: [
        {
          type: 'paragraph',
          content: [
            { type: 'text', text: 'As ' },
            {
              type: 'mention',
              attrs: { type: 'user', id: 1, display: 'Gregor Samsa' },
            },
            {
              type: 'text',
              text:
                ' awoke one morning from uneasy dreams he found himself transformed in his bed into a gigantic insect.',
            },
            {
              type: 'emoji',
              attrs: { id: 11, shortcode: '1F602' },
            },
          ],
        },
        {
          type: 'paragraph',
          content: [
            { type: 'text', text: 'This is a ' },
            { type: 'text', text: 'link', marks: [{ type: 'strong' }] },
            { type: 'text', text: ' card:' },
          ],
        },
        { type: 'ruler' },
        {
          type: 'link_block',
          attrs: {
            url: 'http://ogp.me/',
            image: 'http://ogp.me/logo.png',
            title: 'Open Graph protocol',
            description:
              'The Open Graph protocol enables any web page to become a rich object in a social graph.',
          },
        },
        {
          type: 'paragraph',
          content: [
            {
              type: 'text',
              marks: [
                {
                  type: 'link',
                  attrs: {
                    href: 'https://www.youtube.com/watch?v=vZw35VUBdzo',
                  },
                },
              ],
              text: 'https://www.youtube.com/watch?v=vZw35VUBdzo',
            },
          ],
        },
        {
          type: 'link_media',
          attrs: {
            url: 'https://www.youtube.com/watch?v=vZw35VUBdzo',
          },
        },
        { type: 'paragraph' },
      ],
    };
    this.doc = this.defaultDoc;
    this.json = JSON.stringify(this.doc, null, 2);
  },

  async mounted() {
    const {
      data: { item_id },
    } = await this.$apollo.mutate({
      mutation: getFileUnusedDraftId,
    });
    this.draftId = item_id;
  },

  methods: {
    handleUpdate(opt) {
      this.readJson(opt);
    },

    readJson(opt) {
      this.doc = opt.getJSON();
      this.json = JSON.stringify(opt.getJSON(), null, 2);
    },

    reset() {
      this.doc = this.defaultDoc;
    },
  },
};
</script>

<style lang="scss">
.tui-sample-weka {
  &__json {
    white-space: pre;
  }
}
</style>
