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
    <div class="tui_performAssignmentSchedule__toggle-setting-group">
      <h4>
        {{ $str('schedule_creation_frequency', 'mod_perform') }}
      </h4>
      <div class="tui_performAssignmentScheduleToggles__toggle-group">
        <div class="tui_performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="repeating"
            @input="$emit('update:repeating', $event)"
          >
            <ToggleButtonIcon
              :label="
                $str('schedule_creation_frequency_once_off', 'mod_perform')
              "
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :value="REPEATING_IS_DISABLED"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <RepeatingDisabled :size="300" />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{
                    $str('schedule_creation_frequency_once_off', 'mod_perform')
                  }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="
                $str('schedule_creation_frequency_repeating', 'mod_perform')
              "
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :value="REPEATING_IS_ENABLED"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <RepeatingEnabled :size="300" />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{
                    $str('schedule_creation_frequency_repeating', 'mod_perform')
                  }}
                </div>
              </div>
            </ToggleButtonIcon>
          </ToggleSet>
        </div>
      </div>
    </div>
    <div class="tui_performAssignmentSchedule__toggle-setting-group">
      <h4>
        {{ $str('due_date', 'mod_perform') }}
      </h4>
      <div class="tui_performAssignmentScheduleToggles__toggle-group">
        <div class="tui_performAssignmentScheduleToggles__toggle-set">
          <ToggleSet :value="dueDate" @input="$emit('update:dueDate', $event)">
            <ToggleButtonIcon
              :label="$str('due_date_is_enabled', 'mod_perform')"
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :value="DUE_DATE_IS_ENABLED"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <EndDateEnabled :size="300" />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{ $str('due_date_is_enabled', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="$str('due_date_is_disabled', 'mod_perform')"
              class="tui_performAssignmentScheduleToggles__toggle-button"
              :value="DUE_DATE_IS_DISABLED"
            >
              <div
                class="tui_performAssignmentScheduleToggles__toggle-button-content"
              >
                <EndDateDisabled :size="300" />
                <div class="tui_performAssignmentScheduleToggles__button-label">
                  {{ $str('due_date_is_disabled', 'mod_perform') }}
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
import CalendarIcon from 'totara_core/components/icons/common/Calendar';
import ToggleButtonIcon from 'totara_core/components/buttons/ToggleButtonIcon';
import ToggleSet from 'totara_core/components/buttons/ToggleSet';
import EndDateEnabled from 'mod_perform/components/icons/EndDateEnabled';
import EndDateDisabled from 'mod_perform/components/icons/EndDateDisabled';
import RepeatingEnabled from 'mod_perform/components/icons/RepeatingEnabled';
import RepeatingDisabled from 'mod_perform/components/icons/RepeatingDisabled';
import {
  SCHEDULE_IS_OPEN,
  SCHEDULE_IS_LIMITED,
  SCHEDULE_IS_FIXED,
  SCHEDULE_IS_DYNAMIC,
  DUE_DATE_IS_ENABLED,
  DUE_DATE_IS_DISABLED,
  REPEATING_IS_ENABLED,
  REPEATING_IS_DISABLED,
} from 'mod_perform/constants';

export default {
  components: {
    CalendarIcon,
    ToggleButtonIcon,
    ToggleSet,
    EndDateEnabled,
    EndDateDisabled,
    RepeatingEnabled,
    RepeatingDisabled,
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
    dueDate: {
      required: true,
      type: String,
      validator: val =>
        [DUE_DATE_IS_ENABLED, DUE_DATE_IS_DISABLED].includes(val),
    },
    repeating: {
      required: true,
      type: String,
      validator: val =>
        [REPEATING_IS_ENABLED, REPEATING_IS_DISABLED].includes(val),
    },
  },
  data() {
    return {
      SCHEDULE_IS_OPEN,
      SCHEDULE_IS_LIMITED,
      SCHEDULE_IS_FIXED,
      SCHEDULE_IS_DYNAMIC,
      DUE_DATE_IS_ENABLED,
      DUE_DATE_IS_DISABLED,
      REPEATING_IS_ENABLED,
      REPEATING_IS_DISABLED,
    };
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "due_date",
      "due_date_is_enabled",
      "due_date_is_disabled",
      "schedule_creation_range_and_date_type",
      "schedule_creation_frequency",
      "schedule_creation_frequency_once_off",
      "schedule_creation_frequency_repeating",
      "schedule_is_dynamic",
      "schedule_is_fixed",
      "schedule_is_limited",
      "schedule_is_open"
    ]
  }
</lang-strings>
