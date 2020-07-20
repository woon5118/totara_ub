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
    <div class="tui-performAssignmentScheduleToggles__setting-group">
      <h4 class="tui-performAssignmentSchedule__sectionHeading">
        {{ $str('schedule_creation_range_and_date_type', 'mod_perform') }}
      </h4>
      <div class="tui-performAssignmentScheduleToggles__toggle-group">
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="scheduleIsOpenString"
            @input="$emit('update:scheduleIsOpen', $event === 'true')"
          >
            <ToggleButtonIcon
              :label="$str('schedule_is_limited', 'mod_perform')"
              class="tui-performAssignmentScheduleToggles__toggle-button"
              :text="$str('schedule_is_limited', 'mod_perform')"
              value="false"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <CalendarIcon />
                <div class="tui-performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_limited', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="$str('schedule_is_open', 'mod_perform')"
              class="tui-performAssignmentScheduleToggles__toggle-button"
              :text="$str('schedule_is_open', 'mod_perform')"
              value="true"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <CalendarIcon />
                <CalendarIcon />
                <div class="tui-performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_open', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
          </ToggleSet>
        </div>
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="scheduleIsFixedString"
            @input="$emit('update:scheduleIsFixed', $event === 'true')"
          >
            <ToggleButtonIcon
              :label="$str('schedule_is_fixed', 'mod_perform')"
              class="tui-performAssignmentScheduleToggles__toggle-button"
              value="true"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <div class="tui-performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_fixed', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="$str('schedule_is_dynamic', 'mod_perform')"
              class="tui-performAssignmentScheduleToggles__toggle-button"
              value="false"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <CalendarIcon />
                <CalendarIcon />
                <div class="tui-performAssignmentScheduleToggles__button-label">
                  {{ $str('schedule_is_dynamic', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
          </ToggleSet>
        </div>
      </div>
    </div>
    <div class="tui-performAssignmentSchedule__toggle-setting-group">
      <h4 class="tui-performAssignmentSchedule__sectionHeading">
        {{ $str('schedule_creation_frequency', 'mod_perform') }}
      </h4>
      <div class="tui-performAssignmentScheduleToggles__toggle-group">
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="repeatingIsEnabledString"
            @input="$emit('update:repeatingIsEnabled', $event === 'true')"
          >
            <ToggleButtonIcon
              :label="
                $str('schedule_creation_frequency_once_off', 'mod_perform')
              "
              class="tui-performAssignmentScheduleToggles__toggle-button"
              value="false"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <RepeatingDisabled :size="300" />
                <div class="tui-performAssignmentScheduleToggles__button-label">
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
              class="tui-performAssignmentScheduleToggles__toggle-button"
              value="true"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <RepeatingEnabled :size="300" />
                <div class="tui-performAssignmentScheduleToggles__button-label">
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
    <div class="tui-performAssignmentSchedule__toggle-setting-group">
      <h4 class="tui-performAssignmentSchedule__sectionHeading">
        {{ $str('due_date', 'mod_perform') }}
      </h4>
      <div class="tui-performAssignmentScheduleToggles__toggle-group">
        <div class="tui-performAssignmentScheduleToggles__toggle-set">
          <ToggleSet
            :value="dueDateIsEnabledString"
            @input="$emit('update:dueDateIsEnabled', $event === 'true')"
          >
            <ToggleButtonIcon
              :label="$str('due_date_is_enabled', 'mod_perform')"
              class="tui-performAssignmentScheduleToggles__toggle-button"
              value="true"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <EndDateEnabled :size="300" />
                <div class="tui-performAssignmentScheduleToggles__button-label">
                  {{ $str('due_date_is_enabled', 'mod_perform') }}
                </div>
              </div>
            </ToggleButtonIcon>
            <ToggleButtonIcon
              :label="$str('due_date_is_disabled', 'mod_perform')"
              class="tui-performAssignmentScheduleToggles__toggle-button"
              value="false"
            >
              <div
                class="tui-performAssignmentScheduleToggles__toggle-button-content"
              >
                <EndDateDisabled :size="300" />
                <div class="tui-performAssignmentScheduleToggles__button-label">
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
import CalendarIcon from 'tui/components/icons/common/Calendar';
import EndDateDisabled from 'mod_perform/components/icons/EndDateDisabled';
import EndDateEnabled from 'mod_perform/components/icons/EndDateEnabled';
import RepeatingDisabled from 'mod_perform/components/icons/RepeatingDisabled';
import RepeatingEnabled from 'mod_perform/components/icons/RepeatingEnabled';
import ToggleButtonIcon from 'tui/components/buttons/ToggleButtonIcon';
import ToggleSet from 'tui/components/buttons/ToggleSet';

export default {
  components: {
    CalendarIcon,
    EndDateDisabled,
    EndDateEnabled,
    RepeatingDisabled,
    RepeatingEnabled,
    ToggleButtonIcon,
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
  computed: {
    scheduleIsOpenString() {
      return this.scheduleIsOpen.toString();
    },
    scheduleIsFixedString() {
      return this.scheduleIsFixed.toString();
    },
    repeatingIsEnabledString() {
      return this.repeatingIsEnabled.toString();
    },
    dueDateIsEnabledString() {
      return this.dueDateIsEnabled.toString();
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
