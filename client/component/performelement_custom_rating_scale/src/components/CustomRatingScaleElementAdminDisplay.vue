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
  @module performelement_custom_rating_scale
-->
<template>
  <ElementAdminDisplay
    :type="type"
    :title="title"
    :identifier="identifier"
    :error="error"
    :is-required="isRequired"
    :activity-state="activityState"
    @edit="$emit('edit')"
    @remove="$emit('remove')"
    @display-read="$emit('display-read')"
  >
    <template v-slot:content>
      <RadioGroup :disabled="true" :aria-label="title">
        <Radio
          v-for="(item, index) in data.options"
          :key="index"
          :name="item.name"
          :value="item.value"
          >{{
            $str('answer_output', 'performelement_custom_rating_scale', {
              label: item.value.text,
              count: item.value.score,
            })
          }}</Radio
        >
      </RadioGroup>
    </template>
  </ElementAdminDisplay>
</template>

<script>
import ElementAdminDisplay from 'mod_perform/components/element/ElementAdminDisplay';
import Radio from 'tui/components/form/Radio';
import RadioGroup from 'tui/components/form/RadioGroup';

export default {
  components: {
    ElementAdminDisplay,
    Radio,
    RadioGroup,
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
  "performelement_custom_rating_scale": [
    "answer_output"
  ]
}
</lang-strings>
