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
  <div class="tui-sample-wekaValue">
    <Weka
      v-if="showEditor"
      v-model="content"
      component="editor_weka"
      area="default"
    />
    <hr />
    <Button text="Toggle editor" @click="showEditor = !showEditor" />
    <Button text="Reset" @click="reset" />
    <br />
    <div class="tui-sample-wekaValue__json" v-text="json" />
  </div>
</template>

<script>
import Weka from 'editor_weka/components/Weka';
import Button from 'tui/components/buttons/Button';

export default {
  components: {
    Weka,
    Button,
  },

  data() {
    return {
      showEditor: true,
      content: null,
      json: '',
    };
  },

  watch: {
    content(value) {
      this.json = value && value.getDoc();
    },
  },

  created() {
    this.json = JSON.stringify(this.doc, null, 2);
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
      this.content = null;
    },
  },
};
</script>

<style lang="scss">
.tui-sample-wekaValue {
  &__json {
    white-space: pre;
  }
}
</style>
