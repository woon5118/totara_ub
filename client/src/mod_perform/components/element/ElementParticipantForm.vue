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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
-->
<template>
  <div class="tui-performElementResponse">
    <div v-if="error">{{ error }}</div>
    <FormRow :label="$str('your_response', 'mod_perform')">
      <slot name="content" />
    </FormRow>
  </div>
</template>

<script>
import FormRow from 'tui/components/form/FormRow';
export default {
  components: { FormRow },
  props: {
    name: String,
    type: Object,
    error: String,
  },
};
</script>
<lang-strings>
  {
    "mod_perform": [
      "your_response"
    ]
  }
</lang-strings>
