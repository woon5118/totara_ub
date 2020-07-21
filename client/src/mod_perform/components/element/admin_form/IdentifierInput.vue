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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module mod_perform
-->
<template>
  <FormRow
    :label="$str('reporting_identifier', 'mod_perform')"
    :helpmsg="
      $str('reporting_id_help_text', 'performelement_multi_choice_multi')
    "
  >
    <div>
      <FormText name="identifier" :validations="v => [v.maxLength(1024)]" />
    </div>
  </FormRow>
</template>

<script>
import { FormRow, FormText } from 'tui/components/uniform';

export default {
  components: {
    FormRow,
    FormText,
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "reporting_identifier"
    ],
    "performelement_multi_choice_multi": [
      "reporting_id_help_text"
    ]
  }
</lang-strings>
