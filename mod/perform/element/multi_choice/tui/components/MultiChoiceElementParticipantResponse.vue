<!--
  This file is part of Totara Learn

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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package performelement_multi_choice
-->
<template>
  <ElementParticipantResponse>
    <template v-slot:content>
      <div class="tui-elementEditMultiChoiceParticipantResponse">
        <div
          v-if="answerOption"
          class="tui-elementEditMultiChoiceParticipantResponse__answer"
        >
          {{ answerOption }}
        </div>
        <div
          v-else
          class="tui-elementEditMultiChoiceParticipantResponse__noResponse"
        >
          {{ $str('no_response_submitted', 'performelement_multi_choice') }}
        </div>
      </div>
    </template>
  </ElementParticipantResponse>
</template>

<script>
import ElementParticipantResponse from 'mod_perform/components/element/ElementParticipantResponse';

export default {
  components: {
    ElementParticipantResponse,
  },
  props: {
    data: Object,
    elementData: Object,
  },
  computed: {
    answerOption: {
      get() {
        let optionValue = '';
        if (this.data) {
          this.elementData.options.forEach(item => {
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
    "performelement_multi_choice": [
      "no_response_submitted"
    ]
  }
</lang-strings>
