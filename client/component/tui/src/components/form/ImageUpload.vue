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
  @module tui
-->

<template>
  <Upload
    v-slot:default="{
      inputEvents,
      dragEvents,
      deleteDraft,
      files,
      pickFile,
      isDrag,
    }"
    :repository-id="repositoryId"
    :item-id="itemId"
    :href="href"
    :one-file="true"
    :accepted-types="acceptedTypes"
    :context-id="contextId"
    @load="handleFileLoaded"
    @error="handleError"
  >
    <div
      class="tui-formImageUpload"
      :class="{ 'tui-formImageUpload--highlight': isDrag }"
      v-on="dragEvents"
    >
      <div class="tui-formImageUpload__actions">
        <ButtonIcon
          :text="$str('add', 'totara_core')"
          :aria-label="
            ariaLabelExtension
              ? $str('addextended', 'totara_core', ariaLabelExtension)
              : $str('add', 'totara_core')
          "
          @click="pickFile"
        >
          <AddIcon size="200" />
        </ButtonIcon>
        <ButtonIcon
          v-if="files && files[0]"
          class="tui-formImageUpload__deleteButton"
          :styleclass="{ stealth: true }"
          :aria-label="
            ariaLabelExtension
              ? $str('deleteextended', 'totara_core', ariaLabelExtension)
              : $str('delete', 'totara_core')
          "
          :aria-describedby="ariaDescribedby"
          @click="clearUpload(deleteDraft, files && files[0])"
        >
          <DeleteIcon />
        </ButtonIcon>
      </div>
      <div class="tui-formImageUpload__filearea">
        <input v-show="false" ref="inputFile" type="file" v-on="inputEvents" />
        <div v-if="displayUrl" class="tui-formImageUpload__display">
          <ResponsiveImage :src="displayUrl" />
        </div>
      </div>
      <div
        v-if="files && files[0] && !files[0].done"
        class="tui-formImageUpload__progress"
      >
        <template>
          <Progress :value="Math.round(files[0].progress)" :small="true" />
        </template>
      </div>
    </div>
  </Upload>
</template>

<script>
import Upload from 'tui/components/form/Upload';
import AddIcon from 'tui/components/icons/Add';
import ResponsiveImage from 'tui/components/images/ResponsiveImage';
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import DeleteIcon from 'tui/components/icons/Delete';
import Progress from 'tui/components/progress/Progress';
import { notify } from 'tui/notifications';

export default {
  components: {
    Upload,
    AddIcon,
    ResponsiveImage,
    ButtonIcon,
    DeleteIcon,
    Progress,
  },

  props: {
    href: {
      type: String,
      required: true,
    },
    itemId: {
      type: Number,
      required: true,
    },
    repositoryId: {
      type: Number,
      required: true,
    },
    currentUrl: String,
    defaultUrl: String,
    acceptedTypes: Array,
    ariaDescribedby: String,
    ariaLabelExtension: String,
    contextId: [Number, String],
  },

  data() {
    return {
      selectedImageUrl: null,
    };
  },

  computed: {
    displayUrl() {
      return this.selectedImageUrl || this.currentUrl || this.defaultUrl;
    },
  },

  methods: {
    /**
     *
     * @param {String} url
     */
    handleFileLoaded({ file: { url } }) {
      this.selectedImageUrl = url;
      this.$emit('update', {
        url,
      });
    },

    handleError(e) {
      this.clearUpload();
      let errorMessage = this.$str('error:upload_failed', 'totara_core');
      if (typeof e.error == 'string') {
        errorMessage = e.error;
      }
      notify({ type: 'error', message: errorMessage });
    },

    clearUpload(deleteDraft, file) {
      if (deleteDraft && file) {
        deleteDraft(file);
      }
      this.selectedImageUrl = this.defaultUrl;
      this.$refs.inputFile.value = '';
      this.$emit('update', null);
    },
  },
};
</script>
<lang-strings>
{
  "totara_core": [
    "add",
    "addextended",
    "delete",
    "deleteextended",
    "error:upload_failed"
  ]
}
</lang-strings>

<style lang="scss">
.tui-formImageUpload {
  color: var(--form-input-text-color);
  font-size: var(--form-input-font-size);
  line-height: 1;
  background: var(--form-input-bg-color);

  &__actions {
    display: flex;
    padding: var(--form-input-v-padding) var(--gap-2);
    background: var(--color-neutral-3);
    border-color: var(--form-input-border-color);
    border-top: var(--form-input-border-size) solid;
    border-right: var(--form-input-border-size) solid;
    border-left: var(--form-input-border-size) solid;
  }
  &__deleteButton {
    margin-left: auto;
  }
  &__filearea {
    padding: var(--form-input-v-padding) var(--gap-2);
    border: var(--form-input-border-size) solid;
    border-color: var(--form-input-border-color);

    .tui-formImageUpload--highlight & {
      background: var(--form-input-bg-color-focus);
      border-color: var(--form-input-border-color-focus);
      outline: none;
      box-shadow: var(--form-input-shadow-focus);
    }
  }
  &__display {
    .tui-responsiveImage {
      max-height: 25rem;
    }
  }
  &__progress {
    padding: var(--form-input-v-padding) var(--gap-2);
  }
}
</style>
