<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Jaron Steenson jaron.steenson@totaralearning.com>
  @package mod_perform
-->

<template>
  <div class="tui_performAssignmentScheduleToggles">
    <div class="tui_performAssignmentScheduleToggles__setting-group">
      <h4>
        {{ $str('schedule_creation_range_and_date_type', 'mod_perform') }}
      </h4>
      <div class="tui_performAssignmentScheduleToggles__toggle-group">
        <div class="tui_performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="openLimited"
            @input="$emit('update:openLimited', $event)"
          >
            <ToggleButtonIcon
              :label="$str('schedule_is_limited', 'mod_perform')"
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :text="$str('schedule_is_limited', 'mod_perform')"
              :value="SCHEDULE_IS_LIMITED"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <CalendarIcon />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_limited', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="$str('schedule_is_open', 'mod_perform')"
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :text="$str('schedule_is_open', 'mod_perform')"
              :value="SCHEDULE_IS_OPEN"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <CalendarIcon />
                <CalendarIcon />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_open', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
          </ToggleSet>
        </div>
        <div class="tui_performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="fixedDynamic"
            @input="$emit('update:fixedDynamic', $event)"
          >
            <ToggleButtonIcon
              :label="$str('schedule_is_fixed', 'mod_perform')"
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :value="SCHEDULE_IS_FIXED"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_fixed', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="$str('schedule_is_dynamic', 'mod_perform')"
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :value="SCHEDULE_IS_DYNAMIC"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <CalendarIcon />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_dynamic', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
          </ToggleSet>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import CalendarIcon from 'mod_perform/components/manage_activity/assignment/schedule/icon/Calendar';
import ToggleButtonIcon from 'totara_core/components/buttons/ToggleButtonIcon';
import ToggleSet from 'totara_core/components/buttons/ToggleSet';
import {
  SCHEDULE_IS_OPEN,
  SCHEDULE_IS_LIMITED,
  SCHEDULE_IS_FIXED,
  SCHEDULE_IS_DYNAMIC,
} from 'mod_perform/constants';

export default {
  components: {
    CalendarIcon,
    ToggleButtonIcon,
    ToggleSet,
  },
  props: {
    fixedDynamic: {
      required: true,
      type: String,
      validator: val => [SCHEDULE_IS_FIXED, SCHEDULE_IS_DYNAMIC].includes(val),
    },
    openLimited: {
      required: true,
      type: String,
      validator: val => [SCHEDULE_IS_OPEN, SCHEDULE_IS_LIMITED].includes(val),
    },
  },
  data() {
    return {
      SCHEDULE_IS_OPEN,
      SCHEDULE_IS_LIMITED,
      SCHEDULE_IS_FIXED,
      SCHEDULE_IS_DYNAMIC,
    };
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "schedule_creation_range_and_date_type",
      "schedule_is_dynamic",
      "schedule_is_fixed",
      "schedule_is_limited",
      "schedule_is_open"
    ]
  }
</lang-strings>
