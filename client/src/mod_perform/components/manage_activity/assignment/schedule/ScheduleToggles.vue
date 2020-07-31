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

  @author Jaron Steenson jaron.steenson@totaralearning.com>
  @module mod_perform
-->

<template>
  <div class="tui-performAssignmentScheduleToggles">
    <div class="tui-performAssignmentScheduleToggles__setting">
      <h4 class="tui-performAssignmentSchedule__sectionHeading">
        {{ $str('schedule_creation_range_and_date_type', 'mod_perform') }}
      </h4>
      <div class="tui-performAssignmentScheduleToggles__toggle-group">
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="scheduleIsOpen"
            :aria-label="$str('schedule_creation_range', 'mod_perform')"
            :large="true"
            @input="$emit('update:scheduleIsOpen', $event)"
          >
            <ToggleButton
              :value="false"
              :aria-label="$str('schedule_is_limited', 'mod_perform')"
              :text="$str('schedule_is_limited', 'mod_perform')"
            >
              <CalendarIcon />
            </ToggleButton>

            <ToggleButton
              :value="true"
              :aria-label="$str('schedule_is_open', 'mod_perform')"
              :text="$str('schedule_is_open', 'mod_perform')"
            >
              <CalendarIcon />
            </ToggleButton>
          </ToggleSet>
        </div>
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="scheduleIsFixed"
            :aria-label="$str('schedule_creation_date_type', 'mod_perform')"
            :large="true"
            @input="$emit('update:scheduleIsFixed', $event)"
          >
            <ToggleButton
              :value="true"
              :aria-label="$str('schedule_is_fixed', 'mod_perform')"
              :text="$str('schedule_is_fixed', 'mod_perform')"
            >
              <CalendarIcon />
            </ToggleButton>

            <ToggleButton
              :value="false"
              :aria-label="$str('schedule_is_dynamic', 'mod_perform')"
              :text="$str('schedule_is_dynamic', 'mod_perform')"
            >
              <CalendarIcon />
            </ToggleButton>
          </ToggleSet>
        </div>
      </div>
    </div>
    <div class="performAssignmentScheduleToggles__setting">
      <h4 class="tui-performAssignmentSchedule__sectionHeading">
        {{ $str('schedule_creation_frequency', 'mod_perform') }}
      </h4>
      <div class="tui-performAssignmentScheduleToggles__toggle-group">
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="repeatingIsEnabled"
            :aria-label="$str('schedule_creation_frequency', 'mod_perform')"
            :large="true"
            @input="$emit('update:repeatingIsEnabled', $event)"
          >
            <ToggleButton
              :value="false"
              :aria-label="
                $str('schedule_creation_frequency_once_off', 'mod_perform')
              "
              :text="
                $str('schedule_creation_frequency_once_off', 'mod_perform')
              "
            >
              <RepeatingDisabled />
            </ToggleButton>

            <ToggleButton
              :value="true"
              :aria-label="
                $str('schedule_creation_frequency_repeating', 'mod_perform')
              "
              :text="
                $str('schedule_creation_frequency_repeating', 'mod_perform')
              "
            >
              <RepeatingEnabled />
            </ToggleButton>
          </ToggleSet>
        </div>
      </div>
    </div>
    <div class="performAssignmentScheduleToggles__setting">
      <h4 class="tui-performAssignmentSchedule__sectionHeading">
        {{ $str('due_date', 'mod_perform') }}
      </h4>
      <div class="tui-performAssignmentScheduleToggles__toggle-group">
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="dueDateIsEnabled"
            :aria-label="$str('due_date', 'mod_perform')"
            :large="true"
            @input="$emit('update:dueDateIsEnabled', $event)"
          >
            <ToggleButton
              :value="true"
              :aria-label="$str('due_date_is_enabled', 'mod_perform')"
              :text="$str('due_date_is_enabled', 'mod_perform')"
            >
              <EndDateEnabled />
            </ToggleButton>

            <ToggleButton
              :value="false"
              :aria-label="$str('due_date_is_disabled', 'mod_perform')"
              :text="$str('due_date_is_disabled', 'mod_perform')"
            >
              <EndDateDisabled />
            </ToggleButton>
          </ToggleSet>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import CalendarIcon from 'tui/components/icons/common/Calendar';
import EndDateDisabled from 'mod_perform/components/icons/EndDateDisabled';
import EndDateEnabled from 'mod_perform/components/icons/EndDateEnabled';
import RepeatingDisabled from 'mod_perform/components/icons/RepeatingDisabled';
import RepeatingEnabled from 'mod_perform/components/icons/RepeatingEnabled';
import ToggleButton from 'tui/components/toggle/ToggleButton';
import ToggleSet from 'tui/components/toggle/ToggleSet';

export default {
  //
  components: {
    CalendarIcon,
    EndDateDisabled,
    EndDateEnabled,
    RepeatingDisabled,
    RepeatingEnabled,
    ToggleButton,
    ToggleSet,
  },
  props: {
    scheduleIsOpen: {
      required: true,
      type: Boolean,
    },
    scheduleIsFixed: {
      required: true,
      type: Boolean,
    },
    repeatingIsEnabled: {
      required: true,
      type: Boolean,
    },
    dueDateIsEnabled: {
      required: true,
      type: Boolean,
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "due_date",
      "due_date_is_disabled",
      "due_date_is_enabled",
      "schedule_creation_range",
      "schedule_creation_date_type",
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
