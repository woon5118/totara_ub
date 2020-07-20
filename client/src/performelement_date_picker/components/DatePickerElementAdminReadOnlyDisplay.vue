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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module performelement_date_picker
-->
<template>
  <ElementAdminReadOnlyDisplay
    :type="type"
    :title="title"
    :error="error"
    :identifier="identifier"
    :is-required="isRequired"
    :activity-state="activityState"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
    @display="$emit('display')"
  />
</template>

<script>
import ElementAdminReadOnlyDisplay from 'mod_perform/components/element/ElementAdminReadOnlyDisplay';

export default {
  components: {
    ElementAdminReadOnlyDisplay,
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
