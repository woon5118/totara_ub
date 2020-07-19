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
import Field from 'totara_core/components/reform/Field';
import FieldError from 'totara_core/components/form/FieldError';

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
