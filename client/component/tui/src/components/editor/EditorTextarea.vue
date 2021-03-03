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
  <div class="tui-editorTextarea">
    <Textarea
      :value="value && value.content"
      class="tui-editorTextarea__textarea"
      char-length="full"
      rows="8"
      @input="handleInput"
    />
  </div>
</template>

<script>
import Textarea from 'tui/components/form/Textarea';

export default {
  components: {
    Textarea,
  },

  props: {
    value: {
      type: Object,
    },
  },

  mounted() {
    this.$emit('ready');
  },

  methods: {
    handleInput(val) {
      this.$emit('input', { content: val });
    },
  },
};
</script>

<style lang="scss">
.tui-editorTextarea {
  // stretch child input to be full height if the editor is given a specific height
  display: flex;
  // expand to full width if in horizontal flex
  width: 100%;
}
</style>
