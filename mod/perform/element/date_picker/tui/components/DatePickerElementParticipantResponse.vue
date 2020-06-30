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

  @author Angela Kuznetsova <angela.kuznetsova@totaralearning.com>
  @package performelement_date_picker
-->
<template>
  <div class="tui-elementEditDatePickerParticipantResponse">
    <div
      v-if="answerDate"
      class="tui-elementEditDatePickerParticipantResponse__answer"
    >
      {{ answerDate }}
    </div>
    <div
      v-else
      class="tui-elementEditDatePickerParticipantResponse__noResponse"
    >
      {{ $str('no_response_submitted', 'performelement_date_picker') }}
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
    answerDate: {
      get() {
        let options = { day: 'numeric', month: 'long', year: 'numeric' };
        // TODO: replace with globalConfig.locale when it is added
        let _locale = this.$str('locale', 'langconfig');
        let _localeJs = _locale.replace('_', '-');
        _localeJs = _localeJs.replace(/\..*/, '');
        if (this.data && this.data.date) {
          return new Intl.DateTimeFormat(_localeJs, options).format(
            new Date(this.data.date.iso)
          );
        }
        return '';
      },
    },
  },
};
</script>
<lang-strings>
  {
    "performelement_date_picker": [
        "no_response_submitted"
    ],
    "langconfig": [
        "locale"
    ]
  }
</lang-strings>
