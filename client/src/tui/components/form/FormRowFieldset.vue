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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module totara_core
-->

<template>
  <fieldset class="tui-formRowFieldset">
    <FormRow label-legend v-bind="$attrs">
      <template v-if="$scopedSlots.default" v-slot="slotProps">
        <slot v-bind="slotProps" />
      </template>
    </FormRow>
  </fieldset>
</template>

<script>
// Components
import FormRow from 'totara_core/components/form/FormRow';

export default {
  components: {
    FormRow,
  },

  inheritAttrs: false,
};
</script>
