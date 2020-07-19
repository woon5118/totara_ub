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

  @author Simon Chester <simon.chester@totaralearning.com>
  @module tui
-->

<template>
  <Field
    v-slot="slotProps"
    :name="name"
    :validate="validate"
    :validations="validations"
  >
    <div
      class="tui-formField"
      :class="[hasError(slotProps) && 'tui-context-invalid']"
    >
      <slot
        v-bind="slotProps"
        :error-id="hasError(slotProps) ? $id('error') : false"
      />
      <FieldError
        :id="$id('error')"
        :error="computeError(slotProps)"
        :dismissable="dismissable"
        @dismiss="dismissError"
      />
    </div>
  </Field>
</template>

<script>
import Field from 'tui/components/reform/Field';
import FieldError from 'tui/components/form/FieldError';

export default {
  components: {
    Field,
    FieldError,
  },

  props: {
    name: {
      type: [String, Number, Array],
      required: true,
    },
    dismissable: Boolean,
    error: String,

    validate: Function,
    validations: [Function, Array],
  },

  methods: {
    hasError(slotProps) {
      return !!(this.error || slotProps.error);
    },

    computeError(slotProps) {
      const error = this.error || slotProps.error;
      return error && error.toString();
    },

    dismissError() {
      // Todo
    },
  },
};
</script>
