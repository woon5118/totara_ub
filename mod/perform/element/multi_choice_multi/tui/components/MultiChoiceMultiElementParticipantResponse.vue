<!--
  This file is part of Totara Perform

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
  @package performelement_multi_choice_multi
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
