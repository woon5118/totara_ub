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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @module performelement_custom_rating_scale
-->
<template>
  <div class="tui-elementEditCustomRatingScaleParticipantResponse">
    <div
      v-if="Object.keys(answerOption).length > 0"
      class="tui-elementEditCustomRatingScaleParticipantResponse__answer"
    >
      {{ answerOption }}
    </div>
    <div
      v-else-if="Object.keys(answerOption).length === 0"
      class="tui-elementEditCustomRatingScaleParticipantResponse__noResponse"
    >
      {{ $str('no_response_submitted', 'performelement_custom_rating_scale') }}
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
    answerOption: {
      get() {
        let optionValue = {};
        if (this.data) {
          this.element.data.options.forEach(item => {
            if (item.name == this.data.answer_option) {
              optionValue = this.$str(
                'answer_output',
                'performelement_custom_rating_scale',
                {
                  label: item.value.text,
                  count: item.value.score,
                }
              );
            }
          });
        }
        return optionValue;
      },
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_custom_rating_scale": [
      "answer_output",
      "no_response_submitted"
    ]
  }
</lang-strings>
