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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module performelement_short_text
-->
<template>
  <div class="tui-shortTextElementParticipantResponse">
    <div
      v-if="answerText"
      class="tui-shortTextElementParticipantResponse__answer"
    >
      {{ answerText }}
    </div>
    <div v-else class="tui-shortTextElementParticipantResponse__noResponse">
      {{
        $str(
          'short_text_response_no_response_submitted',
          'performelement_short_text'
        )
      }}
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
  "performelement_short_text": [
    "short_text_response_no_response_submitted"
  ]
  }
</lang-strings>
