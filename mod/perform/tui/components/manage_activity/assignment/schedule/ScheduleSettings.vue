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
  <ScheduleSettingContainer :title="title">
    <p
      v-if="isOpen"
      class="tui-performAssignmentScheduleSettingContainer__preamble"
    >
      {{ $str('schedule_range_date_preamble_with_from', 'mod_perform') }}
    </p>
    <p v-else class="tui-performAssignmentScheduleSettingContainer__preamble">
      {{ $str('schedule_range_date_preamble', 'mod_perform') }}
    </p>
    <FixedScheduleRangeSelector
      v-if="isFixed"
      path="scheduleFixed"
      :has-range="!isOpen"
    />
    <RelativeDateSelector
      v-else
      path="scheduleDynamic"
      has-direction
      :has-range="!isOpen"
      :dynamic-date-sources="dynamicDateSources"
    />
    <div
      v-if="isOpen && isFixed"
      class="tui-performAssignmentScheduleSettingContainer__onwards"
    >
      {{ $str('schedule_date_range_onwards', 'mod_perform') }}
    </div>

    <FormScope v-if="!isFixed" path="scheduleDynamic">
      <div class="tui-performAssignmentScheduleSettings__use-anniversary">
        <FormCheckbox name="use_anniversary">
          {{ $str('schedule_use_anniversary_label', 'mod_perform') }}
        </FormCheckbox>
      </div>
    </FormScope>
  </ScheduleSettingContainer>
</template>

<script>
import RelativeDateSelector from 'mod_perform/components/manage_activity/assignment/schedule/RelativeDateSelector';
import ScheduleSettingContainer from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettingContainer';
import FixedScheduleRangeSelector from 'mod_perform/components/manage_activity/assignment/schedule/FixedScheduleRangeSelector';
import FormCheckbox from 'totara_core/components/uniform/FormCheckbox';
import FormScope from 'totara_core/components/reform/FormScope';

export default {
  components: {
    FixedScheduleRangeSelector,
    FormCheckbox,
    FormScope,
    RelativeDateSelector,
    ScheduleSettingContainer,
  },
  props: {
    isOpen: {
      type: Boolean,
      required: true,
    },
    isFixed: {
      type: Boolean,
      required: true,
    },
    dynamicDateSources: {
      type: Array,
      required: true,
    },
  },
  computed: {
    title() {
      if (this.isOpen && this.isFixed) {
        return this.$str('schedule_range_heading_open_fixed', 'mod_perform'); // Open-ended range defined by fixed dates
      } else if (!this.isOpen && this.isFixed) {
        return this.$str('schedule_range_heading_limited_fixed', 'mod_perform'); // Limited creation range defined by fixed dates
      } else if (this.isOpen && !this.isFixed) {
        return this.$str('schedule_range_heading_open_dynamic', 'mod_perform'); // Open-ended creation range defined by dynamic dates
      } else {
        return this.$str(
          'schedule_range_heading_limited_dynamic',
          'mod_perform'
        ); // Limited creation range defined by dynamic dates
      }
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "schedule_date_range_onwards",
      "schedule_range_date_preamble",
      "schedule_range_date_preamble_with_from",
      "schedule_range_heading_limited_dynamic",
      "schedule_range_heading_limited_fixed",
      "schedule_range_heading_open_dynamic",
      "schedule_range_heading_open_fixed",
      "user_creation_date",
      "schedule_use_anniversary_label"
    ]
  }
</lang-strings>
