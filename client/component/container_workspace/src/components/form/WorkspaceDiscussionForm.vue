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
  @module container_workspace
-->
<template>
  <Form :vertical="true" input-width="full" class="tui-workspaceDiscussionForm">
    <FormRow
      v-slot="{ id }"
      :hidden="true"
      :label="$str('discussion', 'container_workspace')"
      :required="true"
    >
      <WekaEditor
        v-if="!$apollo.queries.draftId.loading"
        :id="id"
        :key="editorKey"
        component="container_workspace"
        area="discussion"
        :file-item-id="draftId"
        :instance-id="discussionId"
        :doc="formContent.document"
        :placeholder="$str('start_discussion', 'container_workspace')"
        class="tui-workspaceDiscussionForm__editor"
        @update="handleUpdate"
      />
    </FormRow>
    <ButtonGroup class="tui-workspaceDiscussionForm__buttonGroup">
      <LoadingButton
        :loading="submitting"
        :disabled="disableSubmit"
        :aria-disabled="disableSubmit"
        :aria-label="submitButtonText"
        :text="submitButtonText"
        :primary="true"
        @click.prevent="submit"
      />
      <CancelButton
        v-if="showCancelButton"
        :disabled="submitting"
        @click.prevent="$emit('cancel')"
      />
    </ButtonGroup>
  </Form>
</template>

<script>
import FormRow from 'tui/components/form/FormRow';
import Form from 'tui/components/form/Form';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import CancelButton from 'tui/components/buttons/Cancel';
import LoadingButton from 'totara_engage/components/buttons/LoadingButton';
import WekaEditor from 'editor_weka/components/Weka';
import { FORMAT_JSON_EDITOR } from 'tui/format';
import { debounce, uniqueId } from 'tui/util';

// GraphQL queries
import discussionDraftId from 'container_workspace/graphql/discussion_draft_id';

export default {
  components: {
    FormRow,
    Form,
    ButtonGroup,
    CancelButton,
    LoadingButton,
    WekaEditor,
  },

  props: {
    submitting: Boolean,
    discussionId: {
      type: [String, Number],
      default: null,
    },
    content: {
      type: String,
      default: null,
    },
    contentFormat: {
      type: [String, Number],
      default() {
        return FORMAT_JSON_EDITOR;
      },
    },
    showCancelButton: {
      type: Boolean,
      default: true,
    },
    submitButtonText: {
      type: String,
      default() {
        return this.$str('post', 'moodle');
      },
    },
  },

  apollo: {
    draftId: {
      query: discussionDraftId,
      fetchPolicy: 'network-only',
      variables() {
        return {
          id: this.discussionId,
        };
      },

      update({ draft_id }) {
        return draft_id;
      },
    },
  },

  data() {
    return {
      editorKey: `editor-weka-${uniqueId()}`,
      draftId: null,
      formContent: {
        document: null,
        isEmpty: true,
      },
    };
  },

  computed: {
    /**
     * @return {Boolean}
     */
    disableSubmit() {
      return this.formContent.isEmpty || this.submitting;
    },
  },

  watch: {
    content: {
      immediate: true,
      /**
       * @param {String} value
       */
      handler(value) {
        if (FORMAT_JSON_EDITOR == this.contentFormat) {
          if (!value) {
            this.formContent.document = null;
            this.formContent.isEmpty = true;

            return;
          }

          this.formContent.document = JSON.parse(this.content);
          this.formContent.isEmpty = false;
        }
      },
    },
  },

  methods: {
    submit() {
      this.$emit('submit', {
        content: JSON.stringify(this.formContent.document),
        itemId: this.draftId,
      });

      // Reset back
      this.formContent.document = null;
      this.formContent.isEmpty = true;

      // Changing the editor key so that we can force re-construction of the form.
      this.editorKey = `editor-weka-${uniqueId()}`;
      this.$forceUpdate();
    },

    /**
     *
     * @param {Object} opt
     */
    handleUpdate(opt) {
      this.$_readJson(opt);
    },

    $_readJson: debounce(
      /**
       *
       * @param {Object} opt
       */
      function(opt) {
        this.formContent.document = opt.getJSON();
        this.formContent.isEmpty = opt.isEmpty();
      },
      250,
      { perArgs: false }
    ),
  },
};
</script>
<lang-strings>
  {
    "container_workspace": [
      "discussion",
      "start_discussion"
    ],
    "moodle": [
      "post"
    ]
  }
</lang-strings>
