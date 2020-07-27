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
  <div
    class="tui-spaceImagePicker"
    :class="{
      'tui-spaceImagePicker--loading': $apollo.loading,
    }"
  >
    <Loading v-if="$apollo.loading" />

    <Upload
      v-else
      :repository-id="metadata.repositoryId"
      :item-id="metadata.itemId"
      :href="metadata.url"
      :accepted-types="metadata.acceptTypes"
      :overwrite="true"
      :one-file="true"
      @load="handleFileLoaded"
      @error="handleError"
      @progress="progressing"
    >
      <div
        slot-scope="{ selectEvents, inputEvents }"
        class="tui-spaceImagePicker__image"
        :class="{
          'tui-spaceImagePicker__image--progressing': isUploadImageLoaded,
        }"
        :style="backgroundStyle"
      >
        <input v-show="false" ref="inputFile" type="file" v-on="inputEvents" />

        <div class="tui-spaceImagePicker__image__loader">
          <PageLoader :loading="isUploadImageLoaded" />
        </div>

        <Button
          v-show="!isUploadImageLoaded"
          :aria-label="$str('edit_image', 'container_workspace')"
          :text="$str('edit_image', 'container_workspace')"
          :styleclass="{ small: true }"
          class="tui-spaceImagePicker__image__button"
          v-on="selectEvents"
        />
      </div>
    </Upload>
  </div>
</template>

<script>
import Upload from 'tui/components/form/Upload';
import Button from 'tui/components/buttons/Button';
import Loading from 'tui/components/icons/common/Loading';
import PageLoader from 'tui/components/loader/Loader';

// GraphQL queries
import getUploadMetadata from 'container_workspace/graphql/upload_metadata';
const has = Object.prototype.hasOwnProperty;

export default {
  components: {
    Upload,
    Button,
    Loading,
    PageLoader,
  },

  props: {
    draftId: [String, Number],
    workspaceId: [Number, String],
  },

  apollo: {
    metadata: {
      query: getUploadMetadata,
      fetchPolicy: 'network-only',
      skip() {
        if (
          !has.call(this.metadata, 'itemId') ||
          !has.call(this.metadata, 'workspaceId')
        ) {
          // No point to skip if metadata is empty.
          return false;
        }

        // Only refetch when the props are different.
        return (
          this.metadata.itemId == this.draftId ||
          this.metadata.workspaceId == this.workspaceId
        );
      },

      variables() {
        return {
          workspace_id: this.workspaceId,
          draft_id: this.draftId,
        };
      },

      update({
        file_area: { item_id, repository_id, url, accept_types },
        image_url,
      }) {
        let parameter = {
          itemId: item_id,
          repositoryId: repository_id,
          acceptTypes: accept_types,
          url: url,
          image: image_url,

          // Need to cache this field as well.
          workspaceId: this.workspaceId,
        };

        this.$emit('ready', parameter);
        return parameter;
      },
    },
  },

  data() {
    return {
      metadata: {},
      uploadedImage: null,
      isUploadImageLoaded: false,
    };
  },

  computed: {
    backgroundStyle() {
      if (this.$apollo.loading) {
        return {
          'background-image': null,
        };
      }

      if (this.uploadedImage) {
        return {
          'background-image': `url("${this.uploadedImage}")`,
        };
      }

      return {
        'background-image': `url("${this.metadata.image}")`,
      };
    },
  },

  methods: {
    /**
     *
     * @param {String} url
     */
    handleFileLoaded({ file: { url } }) {
      this.uploadedImage = url;

      // Reset progress
      this.isUploadImageLoaded = false;

      // Clear any error.
      this.$emit('clear-error');
    },

    handleError() {
      // Reset the uploaded image.
      this.uploadedImage = null;

      // Reset progress
      this.isUploadImageLoaded = false;

      this.$emit(
        'upload-error',
        this.$str('error:upload_image', 'container_workspace')
      );
    },

    progressing() {
      this.isUploadImageLoaded = true;
    },
  },
};
</script>

<lang-strings>
  {
    "container_workspace": [
      "edit_image",
      "error:upload_image"
    ]
  }
</lang-strings>
