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
          $str('custom_rating_options', 'performelement_custom_rating_scale')
        "
      >
        <div class="tui-customRatingScaleElementAdminReadOnlyDisplay__options">
          <div
            v-for="(item, index) in data.options"
            :key="index"
            :name="item.name"
            class="tui-customRatingScaleElementAdminReadOnlyDisplay__options-item"
          >
            {{
              $str('answer_output', 'performelement_custom_rating_scale', {
                label: item.value.text,
                count: item.value.score,
              })
            }}
          </div>
        </div>
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
  "performelement_custom_rating_scale": [
    "answer_output",
    "custom_rating_options"
  ]
  }
</lang-strings>
