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

  @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
  @module performelement_numeric_rating_scale
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
        :label="$str('low_value_label', 'performelement_numeric_rating_scale')"
      >
        {{ data.lowValue }}
      </FormRow>
      <FormRow
        :label="$str('high_value_label', 'performelement_numeric_rating_scale')"
      >
        {{ data.highValue }}
      </FormRow>
      <FormRow
        :label="
          $str('default_number_label', 'performelement_numeric_rating_scale')
        "
      >
        {{ data.defaultValue }}
      </FormRow>
    </template>
  </ElementAdminReadOnlyDisplay>
</template>

<script>
import ElementAdminReadOnlyDisplay from 'mod_perform/components/element/ElementAdminReadOnlyDisplay';
import FormRow from 'tui/components/form/FormRow';

export default {
  components: {
    FormRow,
    ElementAdminReadOnlyDisplay,
  },

  props: {
    title: String,
    identifier: String,
    type: Object,
    data: Object,
    isRequired: Boolean,
    error: String,
    activityState: {
      type: Object,
      required: true,
    },
  },
};
</script>
<lang-strings>
{
  "performelement_numeric_rating_scale": [
    "low_value_label",
    "high_value_label",
    "default_number_label"
  ]
}
</lang-strings>
