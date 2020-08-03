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
  <Modal size="normal" :aria-labelledby="$id('title')">
    <ModalContent
      :title="
        isNew
          ? $str('insert_link', 'editor_weka')
          : $str('edit_link', 'editor_weka')
      "
      :title-id="$id('title')"
      :close-button="true"
    >
      <Form @submit.prevent="confirm">
        <p v-if="error">{{ error }}</p>
        <FormRow v-slot="{ id }" :label="$str('linkurl', 'editor')">
          <InputText :id="id" ref="url" v-model="url" :autofocus="true" />
        </FormRow>
        <FormRow v-slot="{ id }" :label="$str('display_text', 'editor_weka')">
          <InputText
            :id="id"
            :value="display == 'link' ? text : ''"
            :disabled="display != 'link'"
            @input="updateText"
          />
        </FormRow>
        <!-- <FormRow v-slot="{ id }" :label="$str('displayas', 'editor_weka')">
          <RadioGroup :id="id" v-model="display">
            <Radio value="link">
              Embedded media
            </Radio>
            <Radio v-if="!urlIsMedia" value="link_block">
              Link card
            </Radio>
            <Radio v-if="urlIsMedia" value="link_media">
              Plain link
            </Radio>
          </RadioGroup>
        </FormRow> -->
        <input type="submit" :style="{ display: 'none' }" />
      </Form>
      <template v-slot:buttons>
        <ButtonGroup>
          <Button
            :styleclass="{ primary: 'true' }"
            :text="$str('done', 'editor_weka')"
            :disabled="!formValid || loading"
            @click="confirm"
          />
          <ButtonCancel @click="close" />
        </ButtonGroup>
      </template>
    </ModalContent>
  </Modal>
</template>

<script>
import Vue from 'vue';
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import InputText from 'tui/components/form/InputText';
import Modal from 'tui/components/modal/Modal';
import ModalContent from 'tui/components/modal/ModalContent';
import Button from 'tui/components/buttons/Button';
import ButtonCancel from 'tui/components/buttons/Cancel';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';

export default {
  components: {
    Form,
    FormRow,
    InputText,
    Modal,
    ModalContent,
    Button,
    ButtonCancel,
    ButtonGroup,
  },

  props: {
    isNew: Boolean,
    attrs: Object,
    save: Function,
    isMedia: Function,
  },

  data: function() {
    return {
      url: this.attrs.url,
      text: this.attrs.text,
      loading: false,
      display: this.attrs.type || 'link',
      wasText: !!this.attrs.text,
      error: null,
    };
  },

  computed: {
    urlIsMedia() {
      return this.isMedia && this.isMedia(this.url);
    },

    formValue() {
      return { type: this.display, url: this.url, text: this.text };
    },

    formValid() {
      return !!this.url;
    },
  },

  watch: {
    urlIsMedia(media) {
      if (!this.wasText) {
        this.display = media ? 'link_media' : 'link';
      }
    },

    formValue() {
      this.error = null;
    },
  },

  mounted() {
    Vue.nextTick(() => {
      if (this.$refs.url && this.$refs.url.$el) {
        this.$refs.url.$el.focus();
      }
    });
  },

  methods: {
    close() {
      this.$emit('request-close');
    },

    confirm() {
      if (this.loading) {
        return;
      }
      this.loading = true;
      const attrs = this.formValue;
      Promise.resolve(this.save(attrs))
        .then(this.close)
        .catch(e => {
          console.error(e);
        })
        .then(() => (this.loading = false));
    },

    updateText(text) {
      this.text = text;
    },
  },
};
</script>

<lang-strings>
{
  "editor": ["linkurl"],
  "editor_weka": [
    "done",
    "displayas",
    "display_text",
    "edit_link",
    "insert_link",
    "error_no_url_info"
  ],
  "moodle": ["ok", "cancel"]
}
</lang-strings>
