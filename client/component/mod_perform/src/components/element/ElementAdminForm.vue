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
  @module mod_perform
-->
<template>
  <div class="tui-performElementAdminForm">
    <div v-if="error">{{ error }}</div>
    <slot name="content" />
  </div>
</template>

<script>
export default {
  props: {
    type: {
      type: Object,
      required: true,
    },
    activityState: {
      type: Object,
      required: true,
    },
    error: {
      type: String,
    },
  },

  methods: {
    edit() {
      this.$emit('edit');
    },

    remove() {
      this.$emit('remove');
    },
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "activity_action_options",
      "section_delete_element"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-performElementAdminForm {
  padding: var(--gap-2) var(--gap-3);
  background: var(--color-neutral-2);
  border: var(--border-width-thin) solid var(--color-neutral-5);
  border-radius: var(--border-radius-normal);
  cursor: pointer;

  &__type {
    margin-bottom: var(--gap-2);
    font-weight: bold;
  }
}
</style>
