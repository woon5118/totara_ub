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

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @module performelement_multi_choice_multi
-->
<template>
  <div class="tui-elementEditMultiChoiceMultiParticipantResponse">
    <div
      v-if="answerOption"
      class="tui-elementEditMultiChoiceMultiParticipantResponse__answer"
      v-html="answerOption"
    />
    <div
      v-else
      class="tui-elementEditMultiChoiceMultiParticipantResponse__noResponse"
    >
      {{ $str('no_response_submitted', 'performelement_multi_choice_multi') }}
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
        let optionValue = [];
        if (this.data) {
          if (typeof this.element.data.options !== 'undefined') {
            this.element.data.options.forEach(item => {
              if (this.data.answer_option.includes(item.name)) {
                optionValue.push(item.value);
              }
            });
          }
        }
        return optionValue.join('<br/>');
      },
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_multi_choice_multi": [
      "no_response_submitted"
    ]
  }
</lang-strings>
