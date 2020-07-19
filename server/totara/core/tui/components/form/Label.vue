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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_core
-->

<template>
  <component
    :is="legend ? 'legend' : 'label'"
    :id="id"
    :for="forId"
    class="tui-formLabel"
    :class="{
      'tui-formLabel--hidden': hidden,
    }"
  >
    {{ label }}
    <span
      v-if="required"
      class="tui-formLabel__required"
      :aria-label="$str('required', 'moodle')"
      :title="$str('required', 'moodle')"
      v-text="'*'"
    />
  </component>
</template>

<script>
export default {
  props: {
    id: String,
    forId: String,
    legend: Boolean,
    hidden: Boolean,
    label: {
      required: true,
      type: String,
    },
    required: Boolean,
  },
};
</script>

<lang-strings>
{
  "moodle": ["required"]
}
</lang-strings>
