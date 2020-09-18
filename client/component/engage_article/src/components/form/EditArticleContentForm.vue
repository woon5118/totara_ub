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
  @module engage_article
-->
<template>
  <Form class="tui-editArticleContentForm">
    <!-- Loader is for preventing user from typing when the editor is being initialised -->
    <Loader :fullpage="true" :loading="!editorMounted" />
    <Weka
      v-if="!$apollo.loading"
      component="engage_article"
      area="content"
      :instance-id="resourceId"
      :doc="content.doc"
      :file-item-id="draft.file_item_id"
      class="tui-editArticleContentForm__editor"
      @editor-mounted="editorMounted = true"
      @update="handleUpdate"
    />

    <DoneCancelGroup
      :loading="submitting"
      :disabled="content.empty || submitting"
      @done="submit"
      @cancel="$emit('cancel')"
    />
  </Form>
</template>

<script>
import Form from 'tui/components/form/Form';
import Weka from 'editor_weka/components/Weka';
import { debounce } from 'tui/util';
import DoneCancelGroup from 'totara_engage/components/buttons/DoneCancelGroup';
import Loader from 'tui/components/loading/Loader';

// GraphQL queries
import getDraftItem from 'engage_article/graphql/draft_item';

export default {
  components: {
    Form,
    Weka,
    DoneCancelGroup,
    Loader,
  },

  props: {
    resourceId: {
      type: [String, Number],
      required: true,
    },

    submitting: Boolean,
  },

  apollo: {
    draft: {
      query: getDraftItem,
      fetchPolicy: 'network-only',

      variables() {
        return {
          resourceid: this.resourceId,
        };
      },

      result({
        data: {
          draft: { content },
        },
      }) {
        if (content) {
          this.content.doc = JSON.parse(content);
          this.content.empty = false;
        }
      },
    },
  },

  data() {
    return {
      draft: {},
      editorMounted: false,
      content: {
        doc: null,
        empty: true,
      },
    };
  },

  methods: {
    $_readJSON: debounce(
      /**
       * @param {{
       *   getJSON: Function,
       *   isEmpty: Function,
       *   getFileStorageItemId: Function,
       * }} option
       */
      function(option) {
        this.content.doc = option.getJSON();
        this.content.empty = option.isEmpty();
        this.content.itemId = option.getFileStorageItemId();
      },
      100
    ),

    /**
     *
     * @param {Object} option
     */
    handleUpdate(option) {
      this.$_readJSON(option);
    },

    submit() {
      const params = {
        resourceId: this.resourceId,
        content: JSON.stringify(this.content.doc),

        // This seems to be redundant, but lets keep it here, who know in the future, we
        format: this.draft.format,
        itemId: this.content.itemId,
      };

      this.$emit('submit', params);
    },
  },
};
</script>
