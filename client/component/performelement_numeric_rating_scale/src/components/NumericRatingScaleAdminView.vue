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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module performelement_numeric_rating_scale
-->

<template>
  <div class="tui-numericRatingScaleAdminView">
    <Form input-width="full" :vertical="true">
      <FormRow>
        <Range
          :char-length="30"
          :default-value="data.defaultValue"
          :disabled="true"
          :max="data.highValue"
          :min="data.lowValue"
          :show-labels="false"
        />
      </FormRow>
      <FormRow>
        <InputNumber :char-length="20" />
      </FormRow>
    </Form>
  </div>
</template>

<script>
import Form from 'tui/components/form/Form';
import FormRow from 'tui/components/form/FormRow';
import InputNumber from 'tui/components/form/InputNumber';
import Range from 'tui/components/form/Range';

export default {
  components: {
    Form,
    FormRow,
    InputNumber,
    Range,
  },

  props: {
    data: Object,
  },
};
</script>
