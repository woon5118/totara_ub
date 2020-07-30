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

  @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performUserActivitySuccess">
    <h2 class="tui-performUserActivitySuccess__header">
      {{ $str('external_participation_success_heading', 'mod_perform') }}
    </h2>

    <p>{{ $str('external_participation_success_message', 'mod_perform') }}</p>
    <p v-if="!isClosed">
      <a :href="reviewUrl">{{
        $str('external_participation_success_review_link', 'mod_perform')
      }}</a>
    </p>
    <p v-else>
      {{ $str('external_participation_success_message_closed', 'mod_perform') }}
    </p>
  </div>
</template>

<script>
export default {
  components: {},
  props: {
    reviewUrl: {
      required: true,
      type: Boolean,
    },
    isClosed: {
      required: true,
      type: Boolean,
    },
  },

  data() {
    return {};
  },

  computed: {},
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "external_participation_success_heading",
      "external_participation_success_message",
      "external_participation_success_message_closed",
      "external_participation_success_review_link"
    ]
  }
</lang-strings>
