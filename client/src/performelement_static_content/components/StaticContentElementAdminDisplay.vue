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
  @module performelement_static_content
-->
<template>
  <ElementAdminDisplay
    :type="type"
    :title="title"
    :error="error"
    :activity-state="activityState"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
    @display-read="$emit('display-read')"
  >
    <template v-slot:content :disabled="true">
      <div class="tui-staticContentElementAdminDisplay">
        {{ data.textValue }}
      </div>
    </template>
  </ElementAdminDisplay>
</template>

<script>
import ElementAdminDisplay from 'mod_perform/components/element/ElementAdminDisplay';

export default {
  components: {
    ElementAdminDisplay,
  },
  props: {
    title: String,
    type: Object,
    data: Object,
    error: String,
    activityState: {
      type: Object,
      required: true,
    },
  },
};
</script>
