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
  @package performelement_numeric_rating_scale
-->

<template>
  <div class="tui-ratingScaleElementParticipantResponse">
    <div
      v-if="answerValue"
      class="tui-ratingScaleElementParticipantResponse__answer"
    >
      {{ answerValue }}
    </div>
    <div v-else class="tui-ratingScaleElementParticipantResponse__noResponse">
      {{ $str('no_response_submitted', 'performelement_numeric_rating_scale') }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: Object,
    element: Object,
  },

  computed: {
    answerValue: {
      get() {
        return this.data ? this.data.answer_value : '';
      },
    },
  },
};
</script>

<lang-strings>
{
  "performelement_numeric_rating_scale": [
    "no_response_submitted"
  ]
}
</lang-strings>
