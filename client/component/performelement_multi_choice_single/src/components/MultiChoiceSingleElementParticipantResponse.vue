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
  @module performelement_multi_choice_single
-->
<template>
  <div class="tui-elementEditMultiChoiceSingleParticipantResponse">
    <div
      v-if="answerOption"
      class="tui-elementEditMultiChoiceSingleParticipantResponse__answer"
    >
      {{ answerOption }}
    </div>
    <div
      v-else
      class="tui-elementEditMultiChoiceSingleParticipantResponse__noResponse"
    >
      {{ $str('no_response_submitted', 'performelement_multi_choice_single') }}
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
        let optionValue = '';
        if (this.data) {
          this.element.data.options.forEach(item => {
            if (item.name == this.data.answer_option) {
              optionValue = item.value;
            }
          });
        }
        return optionValue;
      },
      set(newValue) {
        if (!this.data) {
          this.data = {};
        }

        this.data.answer_option = newValue;
      },
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_multi_choice_single": [
      "no_response_submitted"
    ]
  }
</lang-strings>
