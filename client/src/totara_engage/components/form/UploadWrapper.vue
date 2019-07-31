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

  @author Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
  @module totara_engage
-->

<template>
  <Upload :repository-id="repositoryId" :item-id="itemId" :href="href">
    <slot />
    <div
      slot-scope="{
        selectEvents,
        inputEvents,
        dragEvents,
        deleteDraft,
        files,
        isDrag,
      }"
      class="tui-engageUploadWrapper"
      :class="{ 'tui-engageUploadWrapper--highlight': isDrag }"
      v-on="dragEvents"
    >
      <input
        v-show="false"
        ref="inputFile"
        type="file"
        multiple
        v-on="inputEvents"
      />
      <a href="#" v-on="selectEvents">Select files</a>
      <ul>
        <li
          v-for="file in files"
          :key="file.name"
          :class="{ 'tui-engageUploadWrapper__file--done': file.done }"
        >
          {{ file.name }} {{ file.progress }}%
          <a href="#" @click.prevent="deleteDraft(file)">&times;</a>
        </li>
      </ul>
    </div>
  </Upload>
</template>

<script>
import Upload from 'totara_engage/components/form/Upload';

export default {
  components: {
    Upload,
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
  },
};
</script>
