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
      <UnsavedChangesWarning
        v-if="!content.isEmpty && !submitting"
        :value="content"
      />
      <Weka
        v-if="!$apollo.queries.editorOption.loading"
        v-model="content"
        :area="item.comment_area.toLowerCase()"
        :instance-id="itemId"
        :file-item-id="item.file_draft_id"
        component="totara_comment"
        :options="editorOption"
        class="tui-editCommentReplyForm__editor"
      />

      <SubmitCancelButtonsGroup
        :submit-text="$str('done', 'totara_comment')"
        :disable-submit="content.isEmpty || submitting"
        :size="size"
        @click-submit="submit"
        @click-cancel="$emit('cancel')"
      />
    </template>
  </Form>
</template>

<script>
import Weka from 'editor_weka/components/Weka';
import WekaValue from 'editor_weka/WekaValue';
import Form from 'tui/components/form/Form';
import { SIZE_SMALL, isValid } from 'totara_comment/size';
import SubmitCancelButtonsGroup from 'totara_comment/components/form/group/SubmitCancelButtonsGroup';

import UnsavedChangesWarning from 'totara_engage/components/form/UnsavedChangesWarning';

// GraphQL query
import getDraftItem from 'totara_comment/graphql/get_draft_item';
import getEditorWeka from 'totara_comment/graphql/get_editor_weka_from_id';

export default {
  components: {
    Weka,
    Form,
    SubmitCancelButtonsGroup,
    UnsavedChangesWarning,
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
        this.content = WekaValue.fromDoc(JSON.parse(draftitem.content));
      },

      update({ draftitem }) {
        return draftitem;
      },
    },

    editorOption: {
      query: getEditorWeka,
      variables() {
        return {
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
      content: WekaValue.empty(),
    };
  },

  methods: {
    submit() {
      const params = {
        id: this.itemId,
        content: JSON.stringify(this.content.getDoc()),
        format: this.item.format,
        itemId: this.content.fileStorageItemId,
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

<style lang="scss">
.tui-editCommentReplyForm {
  display: flex;
  flex-direction: column;
  max-width: 100%;

  &__editor {
    max-width: 100%;
  }
}
</style>
