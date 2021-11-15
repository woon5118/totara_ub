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

  @author Riana Rossouw <riana.rossouw@totaralearning.com>
  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @module mod_perform
-->
<template>
  <div class="tui-assignmentScheduleFrequencySettings">
    <h4 class="tui-assignmentScheduleFrequencySettings__title">
      {{ title }}
    </h4>

    <div class="tui-assignmentScheduleFrequencySettings__description">
      {{
        $str(
          isRepeating
            ? 'schedule_repeating_enabled_description'
            : 'schedule_repeating_disabled_description',
          'mod_perform'
        )
      }}
    </div>

    <template v-if="isRepeating">
      <FormScope :validate="frequencyTriggerValidator" path="repeatingValues">
        <div class="tui-assignmentScheduleFrequencySettings__form">
          <FormRowStack>
            <FormRow :label="$str('trigger', 'mod_perform')">
              <FormRadioGroup name="repeatingType">
                <FormRadioWithInput
                  :name="['repeatingOffset', 'creationOffset']"
                  :text="
                    $str(
                      'schedule_repeating_every_time_since_creation',
                      'mod_perform'
                    )
                  "
                  value="creationOffset"
                >
                  <template
                    v-slot="{
                      disabledRadio,
                      nameLabel,
                      setAccessibleLabel,
                      update,
                      value,
                    }"
                  >
                    <RadioDateRange
                      :disabled="disabledRadio"
                      :name="nameLabel"
                      :value="value"
                      @input="update($event)"
                      @accessible-change="
                        a =>
                          setAccessibleLabel(
                            $str(
                              'schedule_repeating_every_time_since_creation_a11y',
                              'mod_perform',
                              {
                                range: a.range,
                                value: a.value,
                              }
                            )
                          )
                      "
                    />
                  </template>
                </FormRadioWithInput>

                <FormRadioWithInput
                  :name="['repeatingOffset', 'creationWhenCompleteOffset']"
                  :text="
                    $str(
                      'schedule_repeating_every_time_after_creation_when_complete',
                      'mod_perform'
                    )
                  "
                  value="creationWhenCompleteOffset"
                >
                  <template
                    v-slot="{
                      disabledRadio,
                      nameLabel,
                      setAccessibleLabel,
                      update,
                      value,
                    }"
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
                              'schedule_repeating_every_time_after_creation_when_complete_a11y',
                              'mod_perform',
                              {
                                range: a.range,
                                value: a.value,
                              }
                            )
                          )
                      "
                    />
                  </template>
                </FormRadioWithInput>

                <FormRadioWithInput
                  :name="['repeatingOffset', 'completeOffset']"
                  :text="
                    $str(
                      'schedule_repeating_every_time_after_completion',
                      'mod_perform'
                    )
                  "
                  value="completeOffset"
                >
                  <template
                    v-slot="{
                      disabledRadio,
                      nameLabel,
                      setAccessibleLabel,
                      update,
                      value,
                    }"
                  >
                    <RadioDateRange
                      :disabled="disabledRadio"
                      :name="nameLabel"
                      :value="value"
                      @input="update($event)"
                      @accessible-change="
                        a =>
                          setAccessibleLabel(
                            $str(
                              'schedule_repeating_every_time_after_completion_a11y',
                              'mod_perform',
                              {
                                range: a.range,
                                value: a.value,
                              }
                            )
                          )
                      "
                    />
                  </template>
                </FormRadioWithInput>
              </FormRadioGroup>
            </FormRow>

            <FormRow
              :label="$str('schedule_repeating_limit_label', 'mod_perform')"
            >
              <FormRadioGroup name="repeatingIsLimited">
                <Radio :value="false">
                  {{ noLimitLabel }}
                </Radio>

                <FormRadioWithInput
                  :name="['repeatingLimit']"
                  :text="
                    $str('schedule_repeating_limit_maximum_of', 'mod_perform')
                  "
                  :value="true"
                >
                  <template
                    v-slot="{
                      disabledRadio,
                      nameLabel,
                      setAccessibleLabel,
                      update,
                      value,
                    }"
                  >
                    <RadioNumberInput
                      :disabled="disabledRadio"
                      :name="nameLabel"
                      :value="value"
                      @input="update($event)"
                      @accessible-change="
                        a =>
                          setAccessibleLabel(
                            $str(
                              'schedule_repeating_limit_maximum_of_a11y',
                              'mod_perform',
                              a
                            )
                          )
                      "
                    />
                  </template>
                </FormRadioWithInput>
              </FormRadioGroup>
            </FormRow>
          </FormRowStack>
        </div>
      </FormScope>
    </template>
  </div>
</template>

<script>
import Radio from 'tui/components/form/Radio';
import RadioDateRange from 'tui/components/form/RadioDateRangeInput';
import RadioNumberInput from 'tui/components/form/RadioNumberInput';

import {
  FormRadioGroup,
  FormRadioWithInput,
  FormRow,
  FormRowStack,
  FormScope,
} from 'tui/components/uniform';

export default {
  components: {
    FormRadioGroup,
    FormRadioWithInput,
    FormRow,
    FormRowStack,
    FormScope,
    Radio,
    RadioDateRange,
    RadioNumberInput,
  },

  props: {
    isOpen: {
      type: Boolean,
      required: true,
    },
    isRepeating: {
      type: Boolean,
      required: true,
    },
  },

  computed: {
    title() {
      if (this.isRepeating) {
        return this.$str('schedule_repeating_enabled_heading', 'mod_perform');
      } else {
        return this.$str('schedule_repeating_disabled_heading', 'mod_perform');
      }
    },

    noLimitLabel() {
      if (this.isOpen) {
        return this.$str(
          'schedule_repeating_limit_none_open_ended',
          'mod_perform'
        );
      } else {
        return this.$str('schedule_repeating_limit_none', 'mod_perform');
      }
    },
  },

  methods: {
    frequencyTriggerValidator(values) {
      const errors = {};
      const belowMinValueErrorString = this.$str(
        'schedule_repeating_date_min_value',
        'mod_perform'
      );
      const notAWholeNumber = this.$str(
        'schedule_repeating_date_error_value',
        'mod_perform'
      );
      const type = values.repeatingType;
      const offsetValue = Number(values.repeatingOffset[type].value);

      if (values.repeatingLimit) {
        const repeatingLimit = Number(values.repeatingLimit);

        if (!Number.isInteger(repeatingLimit)) {
          errors.repeatingLimit = notAWholeNumber;
        } else if (repeatingLimit < 1) {
          errors.repeatingLimit = belowMinValueErrorString;
        }
      }

      if (!Number.isInteger(offsetValue)) {
        errors.repeatingOffset = { [type]: notAWholeNumber };
      } else if (offsetValue < 1) {
        errors.repeatingOffset = { [type]: belowMinValueErrorString };
      }

      return errors;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "schedule_repeating_date_error_value",
      "schedule_repeating_date_min_value",
      "schedule_repeating_disabled_heading",
      "schedule_repeating_disabled_description",
      "schedule_repeating_enabled_heading",
      "schedule_repeating_enabled_description",
      "schedule_repeating_every_time_after_completion",
      "schedule_repeating_every_time_after_completion_a11y",
      "schedule_repeating_every_time_after_creation_when_complete",
      "schedule_repeating_every_time_after_creation_when_complete_a11y",
      "schedule_repeating_every_time_since_creation",
      "schedule_repeating_every_time_since_creation_a11y",
      "schedule_repeating_limit_label",
      "schedule_repeating_limit_maximum_of",
      "schedule_repeating_limit_maximum_of_a11y",
      "schedule_repeating_limit_none",
      "schedule_repeating_limit_none_open_ended",
      "trigger"
    ]
  }
</lang-strings>

<style lang="scss">
.tui-assignmentScheduleFrequencySettings {
  &__title {
    margin: 0;
    @include tui-font-heading-x-small();
  }

  &__description {
    margin-top: var(--gap-4);
  }

  &__form {
    margin-top: var(--gap-8);
  }
}
</style>
