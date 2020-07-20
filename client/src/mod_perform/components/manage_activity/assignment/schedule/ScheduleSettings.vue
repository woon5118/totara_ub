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
      :dynamic-date-setting-component="dynamicDateSettingComponent"
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
import FormCheckbox from 'tui/components/uniform/FormCheckbox';
import FormScope from 'tui/components/reform/FormScope';

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
    dynamicDateSettingComponent: {
      type: Object,
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
