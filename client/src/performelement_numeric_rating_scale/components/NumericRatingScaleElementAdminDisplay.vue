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
  @package performelement_numeric_rating_scale
-->

<template>
  <ElementAdminDisplay
    class="tui-elementDisplayNumericRatingScale"
    :type="type"
    :title="title"
    :identifier="identifier"
    :error="error"
    :is-required="isRequired"
    :activity-state="activityState"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
  >
    <template v-slot:content>
      <FormRow>
        <div class="tui-elementDisplayNumericRatingScale__range">
          <Range
            :default-value="data.defaultValue"
            :disabled="true"
            :show-labels="false"
            :min="data.lowValue"
            :max="data.highValue"
          />
        </div>
      </FormRow>
    </template>
  </ElementAdminDisplay>
</template>

<script>
import ElementAdminDisplay from 'mod_perform/components/element/ElementAdminDisplay';
import FormRow from 'tui/components/form/FormRow';
import Range from 'tui/components/form/Range';

export default {
  components: {
    ElementAdminDisplay,
    FormRow,
    Range,
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
