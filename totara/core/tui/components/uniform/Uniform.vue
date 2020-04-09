<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
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
import Reform from 'totara_core/components/reform/Reform';
import Form from 'totara_core/components/form/Form';

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
