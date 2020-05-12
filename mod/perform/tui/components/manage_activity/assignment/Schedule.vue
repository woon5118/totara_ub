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

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui_performAssignmentSchedule">
    <h3>{{ $str('activity_instance_creation_heading', 'mod_perform') }}</h3>

    <ScheduleToggles
      :fixed-dynamic.sync="scheduleFixedDynamic"
      :open-limited.sync="scheduleOpenLimited"
      :due-date.sync="scheduleDueDate"
    />
    <h4 class="tui_performAssignmentSchedule__range-type-heading">
      {{ scheduleRangeHeading }}
    </h4>
    <Uniform
      v-slot="{ getSubmitting, reset }"
      :initial-values="initialValues"
      input-width="full"
      @submit="trySave"
    >
      <div class="tui_performAssignmentSchedule__range-type-settings">
        <p>{{ $str('schedule_range_date_preamble', 'mod_perform') }}</p>
        <FixedScheduleSettings
          v-if="scheduleIsFixed"
          :uniform-scope-path="FIXED_SCHEDULE_SCOPE"
          fixed-from-name="fixed_from"
          fixed-to-name="fixed_to"
          :schedule-is-limited-fixed="scheduleIsLimitedFixed"
          :schedule-is-open-fixed="scheduleIsOpenFixed"
        />

        <DynamicScheduleSettings
          v-else-if="scheduleIsDynamic"
          :uniform-scope-path="DYNAMIC_SCHEDULE_SCOPE"
          dynamic-count-from-name="dynamic_count_from"
          dynamic-count-to-name="dynamic_count_to"
          dynamic-unit-name="dynamic_unit"
          dynamic-direction-name="dynamic_direction"
          :schedule-is-dynamic="scheduleIsDynamic"
          :schedule-is-limited-dynamic="scheduleIsLimitedDynamic"
        />
      </div>

      <div class="tui_performAssignmentSchedule__action">
        <ButtonGroup>
          <Button
            :disabled="isSaving"
            :styleclass="{ primary: 'true' }"
            :text="$str('save_changes', 'mod_perform')"
            type="submit"
          />
          <Button
            :disabled="isSaving"
            :text="$str('cancel', 'moodle')"
            @click="reset"
          />
        </ButtonGroup>
      </div>
    </Uniform>
  </div>
</template>

<script>
import { Uniform } from 'totara_core/components/uniform';
import { notify } from 'totara_core/notifications';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Button from 'totara_core/components/buttons/Button';
import UpdateTrackScheduleMutation from 'mod_perform/graphql/update_track_schedule';
import { pick } from 'totara_core/util';
import ScheduleToggles from './schedule/ScheduleToggles';
import FixedScheduleSettings from './schedule/FixedScheduleSettings';
import DynamicScheduleSettings from './schedule/DynamicScheduleSettings';
import {
  NOTIFICATION_DURATION,
  DUE_DATE_IS_ENABLED,
  DUE_DATE_IS_DISABLED,
  SCHEDULE_IS_DYNAMIC,
  SCHEDULE_IS_FIXED,
  SCHEDULE_IS_LIMITED,
  SCHEDULE_IS_OPEN,
  SCHEDULE_DYNAMIC_DIRECTION_BEFORE,
  SCHEDULE_DYNAMIC_UNIT_DAY,
} from 'mod_perform/constants';

const FIXED_SCHEDULE_SCOPE = 'fixed';
const DYNAMIC_SCHEDULE_SCOPE = 'dynamic';

export default {
  components: {
    DynamicScheduleSettings,
    FixedScheduleSettings,
    ScheduleToggles,
    Uniform,
    ButtonGroup,
    Button,
  },
  props: {
    track: {
      type: Object,
      required: true,
    },
  },
  data() {
    let initialValues = {};

    const today = new Date().toISOString().substring(0, 10);

    let fixed_from = today;
    if (this.track.schedule_fixed_from) {
      fixed_from = this.dateFromUnixTime(this.track.schedule_fixed_from)
        .toISOString()
        .substring(0, 10);
    }

    let fixed_to = today;
    if (this.track.schedule_fixed_to) {
      fixed_to = this.dateFromUnixTime(this.track.schedule_fixed_to)
        .toISOString()
        .substring(0, 10);
    }

    initialValues[FIXED_SCHEDULE_SCOPE] = {
      fixed_from,
      fixed_to,
    };

    initialValues[DYNAMIC_SCHEDULE_SCOPE] = {
      dynamic_count_from: this.track.schedule_dynamic_count_from || '0', // Uniform required validation doesn't support int 0 at time of writing.
      dynamic_count_to: this.track.schedule_dynamic_count_to || '0', // Uniform required validation doesn't support int 0 at time of writing.
      dynamic_unit:
        this.track.schedule_dynamic_unit || SCHEDULE_DYNAMIC_UNIT_DAY,
      dynamic_direction:
        this.track.schedule_dynamic_direction ||
        SCHEDULE_DYNAMIC_DIRECTION_BEFORE,
    };

    // Default to limited (Limited vs Open-ended) schedule.
    let scheduleOpenLimited = SCHEDULE_IS_LIMITED;
    if (this.track.schedule_is_open) {
      scheduleOpenLimited = SCHEDULE_IS_OPEN;
    }

    // Default to fixed (Fixed vs Dynamic) schedule.
    let scheduleFixedDynamic = SCHEDULE_IS_DYNAMIC;
    if (this.track.schedule_is_fixed === null || this.track.schedule_is_fixed) {
      scheduleFixedDynamic = SCHEDULE_IS_FIXED;
    }

    let scheduleDueDate = DUE_DATE_IS_DISABLED;
    if (
      this.track.due_date_is_enabled === null ||
      this.track.due_date_is_enabled
    ) {
      scheduleDueDate = DUE_DATE_IS_ENABLED;
    }

    return {
      DUE_DATE_IS_ENABLED,
      DUE_DATE_IS_DISABLED,
      SCHEDULE_IS_OPEN,
      SCHEDULE_IS_LIMITED,
      SCHEDULE_IS_FIXED,
      SCHEDULE_IS_DYNAMIC,
      FIXED_SCHEDULE_SCOPE,
      DYNAMIC_SCHEDULE_SCOPE,
      initialValues,
      scheduleOpenLimited,
      scheduleFixedDynamic,
      isSaving: false,
      scheduleDueDate,
    };
  },
  computed: {
    scheduleRangeHeading() {
      if (this.scheduleIsOpenFixed) {
        return this.$str('schedule_range_heading_open_fixed', 'mod_perform'); // Open-ended range defined by fixed dates
      }

      if (this.scheduleIsLimitedFixed) {
        return this.$str('schedule_range_heading_limited_fixed', 'mod_perform'); // Limited creation range defined by fixed dates
      }

      if (this.scheduleIsLimitedDynamic) {
        return this.$str(
          'schedule_range_heading_limited_dynamic',
          'mod_perform'
        ); // Limited creation range defined by dynamic dates
      }

      return this.$str('schedule_range_heading_open_dynamic', 'mod_perform'); // Open-ended creation range defined by dynamic dates
    },

    scheduleIsLimitedFixed() {
      return this.scheduleIsLimited && this.scheduleIsFixed;
    },
    scheduleIsOpenFixed() {
      return this.scheduleIsOpen && this.scheduleIsFixed;
    },

    scheduleIsLimitedDynamic() {
      return this.scheduleIsLimited && this.scheduleIsDynamic;
    },
    scheduleIsOpenDynamic() {
      return this.scheduleIsOpen && this.scheduleIsDynamic;
    },

    scheduleIsLimited() {
      return this.scheduleOpenLimited === SCHEDULE_IS_LIMITED;
    },
    scheduleIsOpen() {
      return this.scheduleOpenLimited === SCHEDULE_IS_OPEN;
    },
    scheduleIsFixed() {
      return this.scheduleFixedDynamic === SCHEDULE_IS_FIXED;
    },
    scheduleIsDynamic() {
      return this.scheduleFixedDynamic === SCHEDULE_IS_DYNAMIC;
    },
    dueDateIsEnabled() {
      return this.scheduleDueDate === DUE_DATE_IS_ENABLED;
    },
  },
  methods: {
    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Show a generic saving error toast.
     */
    showSuccessNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_success_save_schedule', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Try to persist the schedule to the back end
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave(values) {
      this.isSaving = true;

      try {
        await this.save(this.extractGqlVariables(values));

        this.showSuccessNotification();
      } catch (e) {
        this.showErrorNotification();
        // If something goes wrong during create, allow the user to try again.
        this.isSaving = false;
      } finally {
        this.isSaving = false;
      }
    },

    /**
     * Extract the Uniform values and instance variables
     * into a variable payload for the save mutation.
     * @return {{track_schedule: Object}}
     */
    extractGqlVariables(formValues) {
      const gqlValues = {
        track_id: this.track.id,

        // Toggle values.
        schedule_is_open: this.scheduleIsOpen,
        schedule_is_fixed: this.scheduleIsFixed,
        due_date_is_enabled: this.dueDateIsEnabled,
        // Fixed schedule values.
        schedule_fixed_from: this.getUnixTime(
          formValues[FIXED_SCHEDULE_SCOPE].fixed_from
        ),
        schedule_fixed_to: this.getUnixTime(
          formValues[FIXED_SCHEDULE_SCOPE].fixed_to
        ),

        // Dynamic schedule values.
        schedule_dynamic_count_from: Number(
          formValues[DYNAMIC_SCHEDULE_SCOPE].dynamic_count_from
        ), // Gql does not handle "-1" and an int type.
        schedule_dynamic_count_to: Number(
          formValues[DYNAMIC_SCHEDULE_SCOPE].dynamic_count_to
        ), // Gql does not handle "-1" and an int type.
        schedule_dynamic_unit: formValues[DYNAMIC_SCHEDULE_SCOPE].dynamic_unit,
        schedule_dynamic_direction:
          formValues[DYNAMIC_SCHEDULE_SCOPE].dynamic_direction,
      };

      // Fields common to all permutations of settings.
      const relevantFields = [
        'track_id',
        'schedule_is_open',
        'schedule_is_fixed',
        'due_date_is_enabled',
      ];

      // Add fields specific to the current toggle permutation.
      relevantFields.push(...this.getScheduleTypeSpecificGqlFields());
      const track_schedule = pick(gqlValues, relevantFields);

      return { track_schedule };
    },

    getScheduleTypeSpecificGqlFields() {
      switch (true) {
        case this.scheduleIsOpenFixed:
          return ['schedule_fixed_from'];
        case this.scheduleIsLimitedFixed:
          return ['schedule_fixed_from', 'schedule_fixed_to'];
        case this.scheduleIsOpenDynamic:
          return [
            'schedule_dynamic_count_from',
            'schedule_dynamic_unit',
            'schedule_dynamic_direction',
          ];
        case this.scheduleIsLimitedDynamic:
          return [
            'schedule_dynamic_count_from',
            'schedule_dynamic_count_to',
            'schedule_dynamic_unit',
            'schedule_dynamic_direction',
          ];
      }

      throw new Error('Invalid schedule type');
    },

    /**
     * Calling the mutation
     * @returns {Promise<any>}
     */
    async save(variables) {
      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateTrackScheduleMutation,
        variables,
      });

      return resultData;
    },

    /**
     * Convert a unix time stamp into a Date.
     *
     * @param epoch {string|int|}
     * @return {Date}
     */
    dateFromUnixTime(epoch) {
      return new Date(epoch * 1000);
    },

    /**
     * Convert a date string date into a unix time stamp.
     * If a falsey dateString value is supplied null will be returned.
     * Please note: Note this is most likely temporary until the date picker component is built.
     *
     * @param dataString {string|null|undefined}
     * @return {int|null}
     */
    getUnixTime(dataString) {
      if (!dataString) {
        return null;
      }

      return new Date(dataString).getTime() / 1000;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_instance_creation_heading",
      "due_date",
      "due_date_is_disabled",
      "due_date_is_enabled",
      "save_changes",
      "schedule_range_date_preamble",
      "schedule_range_heading_limited_dynamic",
      "schedule_range_heading_limited_fixed",
      "schedule_range_heading_open_dynamic",
      "schedule_range_heading_open_fixed",
      "toast_error_generic_update",
      "toast_success_save_schedule"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
