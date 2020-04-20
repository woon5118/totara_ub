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
  @package performelement_short_text
-->
<template>
  <ElementParticipantResponse>
    <template v-slot:content>
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
