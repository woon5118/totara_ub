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
  <div class="tui-performAssignmentSchedule">
    <h3 class="tui-performAssignmentSchedule__heading">
      {{ $str('activity_instance_creation_heading', 'mod_perform') }}
    </h3>

    <ScheduleToggles
      :schedule-is-open.sync="scheduleIsOpen"
      :schedule-is-fixed.sync="scheduleIsFixed"
      :due-date-is-enabled.sync="dueDateIsEnabled"
      :repeating-is-enabled.sync="repeatingIsEnabled"
    />

    <Uniform
      v-slot="{ getSubmitting, reset }"
      :initial-values="initialValues"
      input-width="full"
      :validate="validator"
      @submit="trySave"
      @change="onChange"
    >
      <ScheduleSettings :is-open="scheduleIsOpen" :is-fixed="scheduleIsFixed" />

      <DueDateSettings
        :is-enabled="dueDateIsEnabled"
        :is-fixed="dueDateIsFixed"
        :schedule-is-limited-fixed="!scheduleIsOpen && scheduleIsFixed"
      />

      <div class="tui-performAssignmentSchedule__action">
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
// Imports
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import DueDateSettings from 'mod_perform/components/manage_activity/assignment/schedule/DueDateSettings';
import ScheduleSettings from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettings';
import ScheduleToggles from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleToggles';
import UpdateTrackScheduleMutation from 'mod_perform/graphql/update_track_schedule';
import { Uniform } from 'totara_core/components/uniform';

// Util
import { notify } from 'totara_core/notifications';
import {
  NOTIFICATION_DURATION,
  RELATIVE_DATE_DIRECTION_BEFORE,
  RELATIVE_DATE_UNIT_DAY,
} from 'mod_perform/constants';

export default {
  components: {
    Button,
    ButtonGroup,
    DueDateSettings,
    ScheduleSettings,
    ScheduleToggles,
    Uniform,
  },
  props: {
    track: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      scheduleIsOpen: this.track.schedule_is_open,
      scheduleIsFixed: this.track.schedule_is_fixed,
      dueDateIsEnabled: this.track.due_date_is_enabled,
      dueDateIsFixed: this.track.due_date_is_fixed || false,
      repeatingIsEnabled: this.track.repeating_is_enabled,
      isSaving: false,
      initialValues: this.getInitialValues(this.track),
    };
  },
  methods: {
    /**
     * React to changes in the form.
     */
    onChange(values) {
      this.dueDateIsFixed = values.dueDateIsFixed === 'true';
    },

    /**
     * Validate that the dates inputted are logically correct.
     */
    validator(values) {
      if (!this.dueDateIsEnabled) {
        return {};
      }

      const dueDateErrorString = this.$str(
        'due_date_error_must_be_after_creation_date',
        'mod_perform'
      );
      if (this.dueDateIsFixed) {
        let creationEndDate = new Date(values.scheduleFixed.to);
        let dueDate = new Date(values.dueDateFixed.from);
        if (creationEndDate >= dueDate) {
          return {
            dueDateFixed: {
              from: dueDateErrorString,
            },
          };
        }
      } else {
        if (values.dueDateRelative.count <= 0) {
          return {
            dueDateRelative: {
              count: dueDateErrorString,
            },
          };
        }
      }

      return {};
    },

    /**
     * Generate the initial form values.
     * Called upon page load, and after saving.
     * @param {Object} track object
     * @return {Object}
     */
    getInitialValues(track) {
      return {
        // Creation range initial settings
        scheduleFixed: {
          from: this.getInitialDate(track.schedule_fixed_from),
          to: this.getInitialDate(track.schedule_fixed_to),
        },
        scheduleDynamic: {
          count: track.schedule_dynamic_count_from || '0', // Uniform required validation doesn't support int 0 at time of writing.
          count_to: track.schedule_dynamic_count_to || '0', // Uniform required validation doesn't support int 0 at time of writing.
          unit: track.schedule_dynamic_unit || RELATIVE_DATE_UNIT_DAY,
          direction:
            track.schedule_dynamic_direction || RELATIVE_DATE_DIRECTION_BEFORE,
        },

        // Due date initial settings
        dueDateIsFixed: (track.due_date_is_fixed || false).toString(),
        dueDateFixed: {
          from: this.getInitialDate(track.due_date_fixed),
        },
        dueDateRelative: {
          count: track.due_date_relative_count || '14', // Uniform required validation doesn't support int 0 at time of writing.
          unit: track.due_date_relative_unit || RELATIVE_DATE_UNIT_DAY,
        },
      };
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
        const track = await this.save(this.getMutationVariables(values));
        this.initialValues = this.getInitialValues(track);
        this.showSuccessNotification();
        this.isSaving = false;
      } catch (e) {
        this.showErrorNotification();
        this.isSaving = false;
      }
    },

    /**
     * Extract the Uniform values and instance variables
     * into a variable payload for the save mutation.
     * @return {Object}
     */
    getMutationVariables(formValues) {
      const track_schedule = Object.assign(
        {
          track_id: this.track.id,
        },
        this.getScheduleVariables(formValues),
        this.getRepeatingVariables(formValues),
        this.getDueDateVariables(formValues),
        { subject_instance_generation: 'ONE_PER_SUBJECT' }
      );
      return { track_schedule };
    },

    /**
     * Add the required schedule variables for the mutation.
     * @return Object
     */
    getScheduleVariables(form) {
      const gql = {};

      gql.schedule_is_open = this.scheduleIsOpen;
      gql.schedule_is_fixed = this.scheduleIsFixed;

      if (this.scheduleIsFixed) {
        // Fixed start date
        gql.schedule_fixed_from = this.getUnixTime(form.scheduleFixed.from);

        if (!this.scheduleIsOpen) {
          // Fixed start date with closing date
          gql.schedule_fixed_to = this.getUnixTime(form.scheduleFixed.to);
        }
      } else {
        // Dynamic start date
        gql.schedule_dynamic_count_from = Number(form.scheduleDynamic.count); // Gql does not handle "-1" and an int type.
        gql.schedule_dynamic_unit = form.scheduleDynamic.unit;
        gql.schedule_dynamic_direction = form.scheduleDynamic.direction;

        if (!this.scheduleIsOpen) {
          // Dynamic start date with closing date
          gql.schedule_dynamic_count_to = Number(form.scheduleDynamic.count_to); // Gql does not handle "-1" and an int type.
        }
      }

      return gql;
    },

    /**
     * Add the required repeating variables for the mutation.
     * @return Object
     */
    getRepeatingVariables() {
      const gql = {};

      gql.repeating_is_enabled = this.repeatingIsEnabled;

      return gql;
    },

    /**
     * Add the required due date variables for the mutation.
     * @return Object
     */
    getDueDateVariables(form) {
      const gql = {};

      gql.due_date_is_enabled = this.dueDateIsEnabled;

      if (!this.dueDateIsEnabled) {
        return gql;
      }

      if (!this.scheduleIsOpen && this.scheduleIsFixed) {
        gql.due_date_is_fixed = this.dueDateIsFixed;
      }

      if (gql.due_date_is_fixed) {
        gql.due_date_fixed = this.getUnixTime(form.dueDateFixed.from);
      } else {
        gql.due_date_relative_count = Number(form.dueDateRelative.count); // Gql does not handle "-1" and an int type.
        gql.due_date_relative_unit = form.dueDateRelative.unit;
      }

      return gql;
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

      return resultData.mod_perform_update_track_schedule.track;
    },

    /**
     * TODO: Remove this and usages after proper date picker implementation.
     *
     * Convert a date string date into a unix time stamp.
     * If a falsey dateString value is supplied null will be returned.
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

    /**
     * TODO: Remove this and usages after proper date picker implementation.
     *
     * Get the initial time to display from graphql attribute.
     *
     * @param variable {int}
     * @return String date string
     */
    getInitialDate(variable) {
      let date = new Date();
      if (variable) {
        date = new Date(variable * 1000);
      }
      return date.toISOString().substring(0, 10);
    },

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
     * Show a generic saving success toast.
     */
    showSuccessNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_success_save_schedule', 'mod_perform'),
        type: 'success',
      });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_instance_creation_heading",
      "due_date_error_must_be_after_creation_date",
      "save_changes",
      "toast_error_generic_update",
      "toast_success_save_schedule"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
