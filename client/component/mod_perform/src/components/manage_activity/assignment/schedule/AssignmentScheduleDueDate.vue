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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-assignmentScheduleDueDate">
    <h4 class="tui-assignmentScheduleDueDate__title">
      {{ title }}
    </h4>

    <div class="tui-assignmentScheduleDueDate__description">
      {{
        $str(
          isEnabled
            ? 'due_date_enabled_description'
            : 'due_date_disabled_description',
          'mod_perform'
        )
      }}
    </div>

    <template v-if="isEnabled">
      <div class="tui-assignmentScheduleDueDate__form">
        <!-- Due Date is not limited/fixed -->
        <template v-if="!scheduleIsLimitedFixed">
          <FormRow
            :label="$str('due_date_enabled_relative_date_label', 'mod_perform')"
          >
            <FormField
              v-slot="{ labelId, value, update }"
              :name="['dueDateOffset', 'relative']"
            >
              <RadioDateRange
                :aria-labelledby="labelId"
                name="dueDateOffset"
                :value="value"
                @input="update"
              />
            </FormField>
          </FormRow>
        </template>

        <!-- Due Date Is Limited AND Fixed -->

        <FormRowStack v-else>
          <FormRow :label="$str('trigger', 'mod_perform')">
            <FormRadioGroup name="dueDateType">
              <FormRadioWithInput
                v-slot="{
                  disabledRadio,
                  nameLabel,
                  setAccessibleLabel,
                  update,
                  value,
                }"
                :name="['dueDateOffset', 'relative']"
                :text="$str('due_date_enabled_relative_date', 'mod_perform')"
                value="relative"
              >
                <RadioDateRange
                  :disabled="disabledRadio"
                  :value="value"
                  :name="nameLabel"
                  @input="update($event)"
                  @accessible-change="
                    a =>
                      setAccessibleLabel(
                        $str(
                          'due_date_enabled_relative_date_a11y',
                          'mod_perform',
                          {
                            range: a.range,
                            value: a.value,
                          }
                        )
                      )
                  "
                />
              </FormRadioWithInput>
              <Radio value="fixed">
                {{ $str('due_date_enabled_fixed_date', 'mod_perform') }}
              </Radio>
            </FormRadioGroup>
          </FormRow>

          <FormRow v-slot="{ labelId }" :label="$str('date', 'mod_perform')">
            <FieldGroup :aria-labelledby="labelId">
              <FormDateSelector
                :id="$id('fixed-date-from')"
                name="fixedDueDate"
                :disabled="!isFixed"
                :validations="v => [v.required()]"
                type="date"
                has-timezone
              />
            </FieldGroup>
          </FormRow>
        </FormRowStack>
      </div>
    </template>
  </div>
</template>

<script>
import FieldGroup from 'tui/components/form/FieldGroup';
import FormRadioGroup from 'tui/components/uniform/FormRadioGroup';
import Radio from 'tui/components/form/Radio';
import RadioDateRange from 'tui/components/form/RadioDateRangeInput';
import {
  FormDateSelector,
  FormField,
  FormRadioWithInput,
  FormRow,
  FormRowStack,
} from 'tui/components/uniform';

export default {
  components: {
    FieldGroup,
    FormDateSelector,
    FormField,
    FormRadioGroup,
    FormRadioWithInput,
    FormRow,
    FormRowStack,
    Radio,
    RadioDateRange,
  },

  props: {
    isEnabled: {
      type: Boolean,
      required: true,
    },
    isFixed: {
      type: Boolean,
      required: true,
    },
    scheduleIsLimitedFixed: {
      type: Boolean,
      required: true,
    },
  },

  computed: {
    title() {
      return this.isEnabled
        ? this.$str('due_date_enabled', 'mod_perform')
        : this.$str('due_date_disabled', 'mod_perform');
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "date",
      "due_date_disabled",
      "due_date_disabled_description",
      "due_date_enabled",
      "due_date_enabled_description",
      "due_date_enabled_fixed_date",
      "due_date_enabled_relative_date",
      "due_date_enabled_relative_date_a11y",
      "due_date_enabled_relative_date_label",
      "trigger"
    ]
  }
</lang-strings>
