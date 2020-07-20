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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @module performelement_long_text
-->
<template>
  <div class="tui-longTextElementParticipantResponse">
    <div
      v-if="answerText"
      class="tui-longTextElementParticipantResponse__answer"
      v-html="answerText"
    />
    <div v-else class="tui-longTextElementParticipantResponse__noResponse">
      {{ $str('response_no_response_submitted', 'performelement_long_text') }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: Object,
  },
  computed: {
    answerText: {
      get() {
        return this.data ? this.data.answer_text : '';
      },
      set(newValue) {
        if (!this.data) {
          this.data = {};
        }
        this.data.answer_text = newValue;
      },
    },
  },
};
</script>

<lang-strings>
  {
    "performelement_long_text": [
      "response_no_response_submitted"
    ]
  }
</lang-strings>
