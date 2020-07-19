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

  @author Matthias Bonk <matthias.bonk@totaralearning.com>
  @module mod_perform
-->
<template>
  <FormRow :label="$str('reporting_identifier_form_label', 'mod_perform')">
    <div>
      <FormText name="identifier" :validations="v => [v.maxLength(1024)]" />
    </div>
  </FormRow>
</template>

<script>
import { FormRow, FormText } from 'totara_core/components/uniform';

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
      "reporting_identifier_form_label"
    ]
  }
</lang-strings>
