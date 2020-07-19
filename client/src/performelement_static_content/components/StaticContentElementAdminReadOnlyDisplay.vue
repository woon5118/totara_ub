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
  @module performelement_static_content
-->
<template>
  <ElementAdminReadOnlyDisplay
    :type="type"
    :title="title"
    :identifier="identifier"
    :is-required="isRequired"
    :activity-state="activityState"
    @display="$emit('display')"
  >
    <template v-slot:content>
      <FormRow
        :label="
          $str('static_content_placeholder', 'performelement_static_content')
        "
      >
        {{ data.textValue }}
      </FormRow>
    </template>
  </ElementAdminReadOnlyDisplay>
</template>

<script>
import ElementAdminReadOnlyDisplay from 'mod_perform/components/element/ElementAdminReadOnlyDisplay';
import FormRow from 'tui/components/form/FormRow';

export default {
  components: {
    ElementAdminReadOnlyDisplay,
    FormRow,
  },

  props: {
    title: String,
    identifier: String,
    type: Object,
    data: Object,
    isRequired: Boolean,
    activityState: {
      type: Object,
      required: true,
    },
    error: String,
  },
};
</script>
<lang-strings>
  {
  "performelement_static_content": [
    "static_content_placeholder"
  ]
  }
</lang-strings>
