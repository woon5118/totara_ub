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
  @package totara_samples
-->

<template>
  <FormScope :path="path" :validate="validate" :process="process">
    <div>
      <FormRow label="First name" required>
        <FormText name="first" />
      </FormRow>
      <FormRow label="Last name" required>
        <FormText name="last" />
      </FormRow>
    </div>
  </FormScope>
</template>

<script>
import FormScope from 'totara_core/components/reform/FormScope';
import { FormRow, FormText } from 'totara_core/components/uniform';

export default {
  components: {
    FormScope,
    FormRow,
    FormText,
  },

  props: {
    path: [String, Array],
  },

  methods: {
    validate(values = {}) {
      const errors = {};
      if (!values.first) errors.first = 'First name is required';
      if (!values.last) errors.last = 'Last name is required';
      return errors;
    },

    // reshape form value on submission
    // if this was left out the result would simply be an object with { first, last }
    process(values) {
      return `${values.first} ${values.last}`;
    },
  },
};
</script>
