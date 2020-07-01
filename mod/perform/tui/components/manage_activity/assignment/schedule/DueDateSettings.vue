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

  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
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
import FormDateSelector from 'totara_core/components/uniform/FormDateSelector';
import FormRadioGroup from 'totara_core/components/uniform/FormRadioGroup';
import Radio from 'totara_core/components/form/Radio';
import Responsive from 'totara_core/components/responsive/Responsive';
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
