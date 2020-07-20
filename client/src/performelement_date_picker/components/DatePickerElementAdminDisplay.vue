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
  <ElementAdminDisplay
    :type="type"
    :title="title"
    :error="error"
    :identifier="identifier"
    :is-required="isRequired"
    :activity-state="activityState"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
    @display-read="$emit('display-read')"
  >
    <template v-slot:content>
      <DateSelector
        name="date"
        :initial-current-date="false"
        :disabled="true"
      />
    </template>
  </ElementAdminDisplay>
</template>

<script>
import ElementAdminDisplay from 'mod_perform/components/element/ElementAdminDisplay';
import DateSelector from 'tui/components/form/DateSelector';

export default {
  components: {
    ElementAdminDisplay,
    DateSelector,
  },

  props: {
    title: String,
    identifier: String,
    type: Object,
    isRequired: Boolean,
    error: String,
    activityState: {
      type: Object,
      required: true,
    },
  },
};
</script>
