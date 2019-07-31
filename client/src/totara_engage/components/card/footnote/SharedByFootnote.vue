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
  @module totara_engage
-->

<template>
  <div v-show="sharer" class="tui-cardSharedByFootnote">
    <span class="tui-cardSharedByFootnote__text">
      {{ $str('from', 'moodle') }}
    </span>
    <a class="tui-cardSharedByFootnote__url" :href="sharer.url">
      {{ sharer.fullname }}
    </a>
  </div>
</template>

<script>
export default {
  props: {
    instanceId: {
      type: Number,
      required: true,
    },
    component: {
      type: String,
      required: true,
    },
    sharer: {
      type: Object,
      default: () => ({}),
      validator: sharer => ['fullname', 'url'].every(prop => prop in sharer),
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "from"
  ]
}
</lang-strings>
