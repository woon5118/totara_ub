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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module totara_samples
-->

<template>
  <div>
    A date selector component

    <SamplesExample>
      <Uniform v-slot="{ getSubmitting }" :errors="errors" @submit="submit">
        <FormRowFieldset label="Event date">
          <FormDateSelector
            v-modal="dateValue"
            name="date"
            :initial-timezone="'Pacific/Auckland'"
            :initial-current-date="currentDate"
            :initial-custom-date="customDate"
            :disabled="disabled"
            :has-timezone="timezoned"
            :type="isoType"
            :years-midrange="parseInt(midrangeYear)"
            :years-before-midrange="parseInt(midrangeYearBefore)"
            :years-after-midrange="parseInt(midrangeYearAfter)"
            :validations="
              v => [
                v.required(),
                v.date(),
                v.dateMinLimit(minLimit, minLimitErrorMsg),
                v.dateMaxLimit(maxLimit, maxLimitErrorMsg),
              ]
            "
          />
        </FormRowFieldset>
        <FormRowActionButtons :submitting="getSubmitting()" />
      </Uniform>

      <h4>Submitted value:</h4>
      <div v-if="selectedDate">
        {{ selectedDate }}
      </div>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow label="Disabled">
        <RadioGroup v-model="disabled">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <hr />
      <FormRow label="Has timezone">
        <RadioGroup v-model="timezoned">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <hr />
      <FormRow label="Set to custom date">
        <RadioGroup v-model="customDate">
          <Radio :value="false">None</Radio>
          <Radio :value="'1994-02-04'">1994-02-04</Radio>
          <Radio :value="'2004-08-24'">2004-08-24</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow label="Set to current date (Overwritten by Custom date)">
        <RadioGroup v-model="currentDate">
          <Radio :value="true">True</Radio>
          <Radio :value="false">False</Radio>
        </RadioGroup>
      </FormRow>

      <hr />
      <FormRow label="Response type">
        <RadioGroup v-model="isoType">
          <Radio :value="'date'">ISO date</Radio>
          <Radio :value="'dateTime'">ISO date & time</Radio>
        </RadioGroup>
      </FormRow>

      <hr />
      <FormRow v-slot="{ id, label }" :label="'Midrange year'">
        <InputNumber :id="id" v-model="midrangeYear" :placeholder="label" />
      </FormRow>

      <FormRow v-slot="{ id, label }" :label="'Years before midrange'">
        <InputNumber
          :id="id"
          v-model="midrangeYearBefore"
          :placeholder="label"
        />
      </FormRow>

      <FormRow v-slot="{ id, label }" :label="'Years after midrange'">
        <InputNumber
          :id="id"
          v-model="midrangeYearAfter"
          :placeholder="label"
        />
      </FormRow>

      <hr />
      <FormRow label="Date cannot be after">
        <RadioGroup v-model="maxLimit">
          <Radio :value="false">None</Radio>
          <Radio :value="'1994-02-03'">1994-02-03</Radio>
          <Radio :value="'2004-08-23'">2004-08-23</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ id, label }" :label="'Over max limit Error Message'">
        <InputText :id="id" v-model="maxLimitErrorMsg" :placeholder="label" />
      </FormRow>

      <hr />
      <FormRow label="Date cannot be before">
        <RadioGroup v-model="minLimit">
          <Radio :value="false">None</Radio>
          <Radio :value="'1994-02-01'">1994-02-01</Radio>
          <Radio :value="'2004-08-21'">2004-08-21</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ id, label }" :label="'Under min limit Error Message'">
        <InputText :id="id" v-model="minLimitErrorMsg" :placeholder="label" />
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import FormRow from 'totara_core/components/form/FormRow';
import InputNumber from 'totara_core/components/form/InputNumber';
import InputText from 'totara_core/components/form/InputText';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';

import FormRowActionButtons from 'totara_core/components/form/FormRowActionButtons';

import {
  FormDateSelector,
  FormRowFieldset,
  Uniform,
} from 'totara_core/components/uniform';

export default {
  components: {
    FormRow,
    InputNumber,
    InputText,
    FormRowActionButtons,
    FormDateSelector,
    FormRowFieldset,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesExample,
    SamplesPropCtl,

    Uniform,
  },

  data() {
    return {
      currentDate: true,
      customDate: false,
      isoType: 'date',
      dateValue: {},
      disabled: false,
      errors: null,
      midrangeYear: 2010,
      midrangeYearBefore: 20,
      midrangeYearAfter: 20,
      maxLimit: false,
      maxLimitErrorMsg: '',
      minLimit: false,
      minLimitErrorMsg: '',
      selectedDate: {},
      timezoned: true,
      codeTemplate: `<Uniform
  v-slot="{ getSubmitting }"
  :errors="errors"
  @submit="submit"
>
  <FormRowFieldset label="Event date">
    <FormDateSelector
      name="date"
      :initial-current-date="true"
      :initial-custom-date="customDate"
      :has-timezone="true"
      :type="date"
      :validations="
        v => [
          v.required(),
          v.date(),
        ]
      "
    />
  </FormRowFieldset>
  
  ...
</Uniform>`,
      codeScript: `import {
  FormDateSelector,
  FormRowFieldset,
  Uniform,
} from 'totara_core/components/uniform';

export default {
  components: {
    FormDateSelector,
    FormRowFieldset,
    Uniform,
  },
}`,
    };
  },

  methods: {
    submit(values) {
      if (values.date) {
        this.selectedDate = values.date;
      }
    },
  },
};
</script>
