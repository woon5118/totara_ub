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
  @module totara_core
-->

<template>
  <Reform
    v-slot="slotProps"
    :initial-values="initialValues"
    :errors="errors"
    :validate="validate"
    :validation-mode="validationMode"
    @change="$emit('change', $event)"
    @submit="$emit('submit', $event)"
  >
    <Form
      :vertical="vertical"
      :input-width="inputWidth"
      @submit="slotProps.handleSubmit"
    >
      <slot v-bind="slotProps" />
    </Form>
  </Reform>
</template>

<script>
import Reform from 'tui/components/reform/Reform';
import Form from 'tui/components/form/Form';

export default {
  components: {
    Reform,
    Form,
  },

  props: {
    /*
     * Initial values for form fields.
     */
    initialValues: {
      type: [Object, Function],
      default: () => ({}),
    },

    /**
     * External errors to display in form.
     */
    errors: Object,

    /**
     * Root-level validator function.
     */
    validate: Function,

    /**
     * Validation mode.
     *
     * 'auto': smart validation
     * 'submit': only validate on submit
     */
    validationMode: {
      type: String,
      default: 'auto',
      validator: x => ['auto', 'submit'].includes(x),
    },

    vertical: Boolean,

    inputWidth: {
      type: String,
      validator: x => ['full', 'limited'].includes(x),
      default: 'limited',
    },
  },
};
</script>
