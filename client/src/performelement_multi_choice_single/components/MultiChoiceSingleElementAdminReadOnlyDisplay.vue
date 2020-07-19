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
  @module performelement_multi_choice_multi
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
          $str('multi_select_options', 'performelement_multi_choice_multi')
        "
      >
        <div class="tui-multiChoiceSingleElementAdminReadOnlyDisplay__options">
          <div
            v-for="item in data.options"
            :key="item.name"
            class="tui-multiChoiceSingleElementAdminReadOnlyDisplay__options-item"
          >
            {{ item.value }}
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
  "performelement_multi_choice_multi": [
    "multi_select_options"
  ]
  }
</lang-strings>
