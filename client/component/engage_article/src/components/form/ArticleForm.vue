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
  @module engage_article
-->

<template>
  <Form :vertical="true" input-width="full" class="tui-articleForm">
    <FormRow
      v-slot="{ id }"
      :hidden="true"
      :label="$str('articletitle', 'engage_article')"
      :required="true"
      class="tui-articleForm__title"
    >
      <InputText
        :id="id"
        v-model="name"
        name="article-title"
        :maxlength="75"
        :placeholder="$str('entertitle', 'engage_article')"
        :disabled="submitting"
        :required="true"
      />
    </FormRow>

    <div class="tui-articleForm__description">
      <FormRow
        v-slot="{ id }"
        :hidden="true"
        :label="$str('content', 'engage_article')"
        :required="true"
        class="tui-articleForm__description__formRow"
        :is-stacked="false"
      >
        <Weka
          v-if="draftId"
          :id="id"
          component="engage_article"
          area="content"
          :doc="content.doc"
          :file-item-id="draftId"
          :placeholder="$str('entercontent', 'engage_article')"
          @update="handleUpdate"
        />
      </FormRow>

      <div class="tui-articleForm__description__tip">
        <p>{{ $str('contributetip', 'totara_engage') }}</p>
        <Popover position="right">
          <template v-slot:trigger="{ isOpen }">
            <ButtonIcon
              :aria-expanded="isOpen.toString()"
              :aria-label="$str('info', 'moodle')"
              class="tui-articleForm__description__iconButton"
              :styleclass="{
                primary: true,
                small: true,
                transparentNoPadding: true,
              }"
            >
              <InfoIcon />
            </ButtonIcon>
          </template>

          <p class="tui-articleForm__description__tip__content">
            {{ $str('contributetip_help', 'totara_engage') }}
          </p>
        </Popover>
      </div>
    </div>

    <ButtonGroup class="tui-articleForm__buttons">
      <Button
        :loading="submitting"
        :styleclass="{ primary: 'true' }"
        :disabled="disabled"
        :aria-label="$str('createarticleshort', 'engage_article')"
        :text="$str('next', 'moodle')"
        @click="submit"
      />

      <CancelButton :disabled="submitting" @click="$emit('cancel')" />
    </ButtonGroup>
  </Form>
</template>

<script>
import InputText from 'tui/components/form/InputText';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import CancelButton from 'tui/components/buttons/Cancel';
import Button from 'tui/components/buttons/Button';
import Popover from 'tui/components/popover/Popover';
import { debounce } from 'tui/util';
import Weka from 'editor_weka/components/Weka';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import InfoIcon from 'tui/components/icons/Info';

// GraphQL queries
import fileDraftId from 'core/graphql/file_unused_draft_item_id';

export default {
  components: {
    ButtonIcon,
    InputText,
    ButtonGroup,
    Button,
    CancelButton,
    Popover,
    Weka,
    Form,
    FormRow,
    InfoIcon,
  },

  props: {
    articleContent: {
      type: String,
      default: '',
    },

    articleName: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      // Caching the name separately
      name: this.articleName,
      content: {
        // Default state of editor
        doc: null,
        isEmpty: true,
      },

      draftId: null,
      submitting: false,
    };
  },

  computed: {
    disabled() {
      return this.name.length === 0 || this.content.isEmpty;
    },
  },

  watch: {
    articleContent: {
      immediate: true,
      /**
       *
       * @param {String} value
       */
      handler(value) {
        if (!value) {
          return;
        }

        try {
          this.content.doc = JSON.parse(value);
        } catch (e) {
          // Silenced any invalid json string.
          this.content.doc = null;
        }
      },
    },
  },

  async mounted() {
    await this.$_loadDraftId();
  },

  methods: {
    /**
     *
     * @param {Object} opt
     */
    handleUpdate(opt) {
      this.$_readJson(opt);
    },

    async $_loadDraftId() {
      const {
        data: { item_id },
      } = await this.$apollo.mutate({ mutation: fileDraftId });
      this.draftId = item_id;
    },

    $_readJson: debounce(
      /**
       *
       * @param {Object} opt
       */
      function(opt) {
        this.content.doc = opt.getJSON();
        this.content.isEmpty = opt.isEmpty();
      },
      250,
      { perArgs: false }
    ),

    submit() {
      const params = {
        name: this.name,
        content: JSON.stringify(this.content.doc),
        itemId: this.draftId,
      };

      this.$emit('next', params);
      this.$_loadDraftId();
    },
  },
};
</script>

<lang-strings>
  {
    "moodle": [
      "next",
      "info"
    ],
    "totara_core": [
      "save"
    ],
    "engage_article": [
      "entertitle",
      "entercontent",
      "articletitle",
      "content",
      "createarticleshort"
    ],
    "totara_engage": [
      "contributetip",
      "contributetip_help"
    ]
  }
</lang-strings>
