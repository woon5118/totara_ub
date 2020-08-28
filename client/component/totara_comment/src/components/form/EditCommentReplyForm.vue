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
  @module totara_comment
-->
<template>
  <Form class="tui-editCommentReplyForm">
    <template v-if="!$apollo.loading">
      <Weka
        v-if="!$apollo.queries.editorOption.loading"
        :area="item.comment_area"
        :instance-id="itemId"
        :file-item-id="item.file_draft_id"
        component="totara_comment"
        :options="editorOption"
        :doc="content.doc"
        class="tui-editCommentReplyForm__editor"
        @update="update"
      />

      <SubmitCancelButtonsGroup
        :submit-text="$str('done', 'totara_comment')"
        :disable-submit="content.empty || submitting"
        :size="size"
        @click-submit="submit"
        @click-cancel="$emit('cancel')"
      />
    </template>
  </Form>
</template>

<script>
import Weka from 'editor_weka/components/Weka';
import Form from 'tui/components/form/Form';
import { debounce } from 'tui/util';
import { SIZE_SMALL, isValid } from 'totara_comment/size';
import SubmitCancelButtonsGroup from 'totara_comment/components/form/group/SubmitCancelButtonsGroup';

// GraphQL query
import getDraftItem from 'totara_comment/graphql/get_draft_item';
import getEditorWeka from 'totara_comment/graphql/get_editor_weka';

export default {
  components: {
    Weka,
    Form,
    SubmitCancelButtonsGroup,
  },

  apollo: {
    item: {
      query: getDraftItem,
      fetchPolicy: 'network-only',
      variables() {
        return {
          id: this.itemId,
        };
      },

      result({ data: { draftitem } }) {
        this.content.doc = JSON.parse(draftitem.content);
        this.content.empty = false;
      },

      update({ draftitem }) {
        return draftitem;
      },
    },

    editorOption: {
      query: getEditorWeka,
      variables() {
        return {
          component: this.item.component,
          area: this.item.area,
          comment_area: this.item.comment_area.toLowerCase(),
          id: this.itemId,
        };
      },

      update({ editor }) {
        return editor;
      },

      skip() {
        return this.$apollo.queries.item.loading;
      },
    },
  },

  props: {
    size: {
      type: String,
      default() {
        return SIZE_SMALL;
      },

      validator(prop) {
        return isValid(prop);
      },
    },

    /**
     * Either comment's or reply's id.
     */
    itemId: {
      type: [String, Number],
      required: true,
    },

    submitting: Boolean,
  },

  data() {
    return {
      editorOption: null,
      item: {},
      content: {
        doc: null,
        empty: true,
        // Note that this is for file storage item id, not comment nor reply id.
        itemId: null,
      },
    };
  },

  methods: {
    $_readJSON: debounce(
      /**
       *
       * @param {Object} option
       */
      function(option) {
        this.content.doc = option.getJSON();
        this.content.empty = option.isEmpty();
        this.content.itemId = option.getFileStorageItemId();
      },
      100,
      {}
    ),

    /**
     *
     * @param {Object} option
     */
    update(option) {
      this.$_readJSON(option);
    },

    submit() {
      const params = {
        id: this.itemId,
        content: JSON.stringify(this.content.doc),
        format: this.item.format,
        itemId: this.content.itemId,
      };

      this.$emit('update-item', params);
    },
  },
};
</script>

<lang-strings>
  {
    "totara_comment": [
      "done"
    ]
  }
</lang-strings>
