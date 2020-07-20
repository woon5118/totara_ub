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
  @module mod_perform
-->

<template>
  <ScheduleSettingContainer
    class="tui-performAssignmentDueDateSettings"
    :title="title"
  >
    <!-- Due Date Disabled -->
    <template v-if="!isEnabled">{{
      $str('due_date_disabled_description', 'mod_perform')
    }}</template>

    <!-- Due Date is not limited/fixed -->
    <template v-else-if="!scheduleIsLimitedFixed">
      <span>{{
        $str('due_date_enabled_description_before', 'mod_perform')
      }}</span>
      <span>{{ $str('due_date_within', 'mod_perform') }}</span>
      <RelativeDateSelector path="dueDateOffset" />
      <span>{{
        $str('due_date_enabled_description_after', 'mod_perform')
      }}</span>
    </template>

    <!-- Due Date Is Limited AND Fixed -->
    <template v-else>
      <span>{{
        $str('due_date_enabled_description_before', 'mod_perform')
      }}</span>
      <Responsive
        v-slot="{ currentBoundaryName }"
        :breakpoints="[
          { name: 'small', boundaries: [0, 700] },
          { name: null, boundaries: [701, 701] },
        ]"
      >
        <FormRadioGroup name="dueDateIsFixed">
          <Radio
            value="true"
            :class="{
              'tui-performAssignmentDueDateSettings__fixed': true,
              'tui-performAssignmentSchedule__radio-disabled': !isFixed,
            }"
          >
            <span>{{ $str('due_date_by', 'mod_perform') }}</span>
            <FormDateSelector
              v-if="currentBoundaryName !== 'small'"
              :id="$id('fixed-date-from')"
              name="dueDateFixed"
              :disabled="!isFixed"
              :validations="v => [v.required()]"
              type="date"
              has-timezone
            />
          </Radio>
          <FormDateSelector
            v-if="currentBoundaryName === 'small'"
            :id="$id('fixed-date-from')"
            name="dueDateFixed"
            :disabled="!isFixed"
            :validations="v => [v.required()]"
            type="date"
            has-timezone
          />
          <Radio
            value="false"
            :class="{
              'tui-performAssignmentSchedule__radio-disabled': isFixed,
            }"
          >
            <span>{{ $str('due_date_within', 'mod_perform') }}</span>
            <RelativeDateSelector path="dueDateOffset" :disabled="isFixed" />
            <span>{{
              $str('due_date_enabled_description_after', 'mod_perform')
            }}</span>
          </Radio>
        </FormRadioGroup>
      </Responsive>
    </template>
  </ScheduleSettingContainer>
</template>

<script>
import FormDateSelector from 'tui/components/uniform/FormDateSelector';
import FormRadioGroup from 'tui/components/uniform/FormRadioGroup';
import Radio from 'tui/components/form/Radio';
import Responsive from 'tui/components/responsive/Responsive';
import RelativeDateSelector from 'mod_perform/components/manage_activity/assignment/schedule/RelativeDateSelector';
import ScheduleSettingContainer from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettingContainer';

export default {
  components: {
    Responsive,
    FormDateSelector,
    FormRadioGroup,
    Radio,
    RelativeDateSelector,
    ScheduleSettingContainer,
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
      "due_date_by",
      "due_date_disabled",
      "due_date_disabled_description",
      "due_date_enabled",
      "due_date_enabled_description_after",
      "due_date_enabled_description_before",
      "due_date_within"
    ]
  }
</lang-strings>
