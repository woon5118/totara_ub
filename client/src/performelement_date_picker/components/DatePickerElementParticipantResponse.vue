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
  @module performelement_date_picker
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
