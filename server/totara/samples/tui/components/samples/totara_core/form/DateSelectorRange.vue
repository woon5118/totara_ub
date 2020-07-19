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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_samples
-->

<template>
  <div>
    A date selector component for start/end dates

    <SamplesExample>
      <Uniform
        v-slot="{ getSubmitting }"
        :errors="errors"
        :validate="validate"
        @submit="submit"
      >
        <FormRowFieldset label="Start date">
          <FormDateSelector
            v-modal="startValue"
            name="startDate"
            :initial-current-date="true"
            type="date"
            :validations="v => [v.required()]"
          />
        </FormRowFieldset>

        <FormRowFieldset label="End date">
          <FormDateSelector
            v-modal="endValue"
            name="endDate"
            :initial-current-date="true"
            type="date"
            :validations="v => [v.required()]"
          />
        </FormRowFieldset>
        <FormRowActionButtons :submitting="getSubmitting()" />
      </Uniform>

      <h4>Submitted value:</h4>
      <div v-if="selectedStartDate">
        <h5>Start date</h5>
        {{ selectedStartDate }}
      </div>
      <div v-if="selectedEndDate">
        <h5>End date</h5>
        {{ selectedEndDate }}
      </div>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';
import {
  FormDateSelector,
  FormRowFieldset,
  Uniform,
} from 'totara_core/components/uniform';
// Utils
import { isIsoAfter } from 'totara_core/date';

export default {
  components: {
    FormRowActionButtons,
    FormDateSelector,
    FormRowFieldset,
    SamplesCode,
    SamplesExample,
    Uniform,
  },

  data() {
    return {
      errors: null,
      selectedStartDate: {
        iso: '',
      },
      selectedEndDate: {
        iso: '',
      },
      startValue: '',
      endValue: '',
      codeTemplate: `<Uniform
  v-slot="{ getSubmitting }"
  :errors="errors"
  :validate="validate"
  @submit="submit"
>
  <FormRowFieldset label="Start date">
    <FormDateSelector
      v-modal="startValue"
      name="startDate"
      :initial-current-date="true"
      type="date"
      :validations="v => [v.required()]"
    />
  </FormRowFieldset>

  <FormRowFieldset label="End date">
    <FormDateSelector
      v-modal="endValue"
      name="endDate"
      :initial-current-date="true"
      type="date"
      :validations="v => [v.required()]"
    />
  </FormRowFieldset>
  <FormRowActionButtons :submitting="getSubmitting()" />
</Uniform>`,
      codeScript: `import {
  FormDateSelector,
  FormRowFieldset,
  Uniform,
} from 'totara_core/components/uniform';
// Utils
import { isIsoAfter } from 'totara_core/date';

export default {
  components: {
    FormDateSelector,
    FormRowFieldset,
    Uniform,
  },
},

data() {
  return {
    errors: null,
    startValue: '',
    endValue: '',
  },
},

methods: {
  submit(values) {
    if (values.startDate) {
      this.selectedStartDate = values.startDate;
    }
    if (values.endDate) {
      this.selectedEndDate = values.endDate;
    }
  },

  validate(values) {
    const errors = {};

    if (!isIsoAfter(values.endDate.iso, values.startDate.iso)) {
      errors.endDate = 'End date cannot be before start date';
    }
    return errors;
  },
}`,
    };
  },

  methods: {
    submit(values) {
      if (values.startDate) {
        this.selectedStartDate = values.startDate;
      }
      if (values.endDate) {
        this.selectedEndDate = values.endDate;
      }
    },

    validate(values) {
      const errors = {};

      if (values.endDate && values.startDate) {
        if (!isIsoAfter(values.endDate.iso, values.startDate.iso)) {
          errors.endDate = 'End date cannot be before start date';
        }
      }
      return errors;
    },
  },
};
</script>
