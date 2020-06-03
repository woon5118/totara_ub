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
    <p>{{ $str('schedule_range_date_preamble', 'mod_perform') }}</p>
    <span>{{ $str('schedule_date_from', 'mod_perform') }}</span>
    <FixedDateSelector
      v-if="isFixed"
      path="scheduleFixed"
      :has-range="!isOpen"
    />
    <RelativeDateSelector
      v-else
      path="scheduleDynamic"
      has-direction
      :has-range="!isOpen"
    />
    <template v-if="isOpen">
      <span v-if="isFixed">{{
        $str('schedule_date_range_onwards', 'mod_perform')
      }}</span>
      <strong v-else>{{ $str('user_creation_date', 'mod_perform') }}</strong>
    </template>
  </ScheduleSettingContainer>
</template>

<script>
import RelativeDateSelector from 'mod_perform/components/manage_activity/assignment/schedule/RelativeDateSelector';
import ScheduleSettingContainer from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettingContainer';
import FixedDateSelector from 'mod_perform/components/manage_activity/assignment/schedule/FixedDateSelector';

export default {
  components: {
    FixedDateSelector,
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
      "schedule_date_from",
      "schedule_date_range_onwards",
      "schedule_range_date_preamble",
      "schedule_range_heading_limited_dynamic",
      "schedule_range_heading_limited_fixed",
      "schedule_range_heading_open_dynamic",
      "schedule_range_heading_open_fixed",
      "user_creation_date"
    ]
  }
</lang-strings>
